#!/usr/bin/env python3
"""
Generates the student dark theme.

Every student page keeps its own <style> block with hard-coded light colours.
Instead of hand-writing a dark copy of every rule, this script reads each
page's CSS, works out a dark equivalent for every colour (light surfaces →
dark surfaces, dark text → light text, light borders → dark borders, accent
colours kept), and writes the result into a <style id="rk-dark-theme"> block
in that same page, scoped to  html[data-theme="dark"].

It also generates overrides for the Tailwind colour classes the Profile &
Settings pages use (bg-white, text-indigo-900, border-indigo-100, ...) into
resources/views/partials/dark-tailwind.blade.php.

Hand-tuned fixes live in resources/views/partials/dark-fixes.blade.php and
are never touched by this script.

Run again after changing a page's styles:
    pip install tinycss2
    python3 scripts/dark-theme/generate.py
"""
import colorsys
import json
import os
import re
import sys

import tinycss2

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
VIEWS = os.path.join(ROOT, 'resources', 'views')

PAGES = [
    'partials/app-nav.blade.php',
    'homepage.blade.php',
    'chat.blade.php',
    'timetable.blade.php',
    'reminders.blade.php',
    'reminders-history.blade.php',
    'student/profile.blade.php',
    'student/edit-profile.blade.php',
    'student/change-password.blade.php',
    'student/privacy-security.blade.php',
    'student/notification-settings.blade.php',
    'student/language.blade.php',
    'student/appearance.blade.php',
    'student/help-support.blade.php',
    'student/about.blade.php',
]

DARK = 'html[data-theme="dark"]'
# Rules that must look the same in both themes (e.g. the light/dark previews on the Appearance page)
SKIP_SELECTORS = re.compile(r'\.tp-|\.theme-preview')
BLOCK_RE = re.compile(r'\n?<style id="rk-dark-theme">.*?</style>\n?', re.S)

NAMED = {
    'white': (255, 255, 255, 1.0), 'black': (0, 0, 0, 1.0),
    'transparent': None, 'currentcolor': None, 'inherit': None, 'none': None,
}

# ---------------------------------------------------------------- colours

def parse_color(tok):
    t = tok.strip().lower()
    if t in NAMED:
        return NAMED[t]
    m = re.fullmatch(r'#([0-9a-f]{3,8})', t)
    if m:
        h = m.group(1)
        if len(h) in (3, 4):
            h = ''.join(c * 2 for c in h)
        r, g, b = int(h[0:2], 16), int(h[2:4], 16), int(h[4:6], 16)
        a = int(h[6:8], 16) / 255 if len(h) == 8 else 1.0
        return (r, g, b, a)
    m = re.fullmatch(r'rgba?\(([^)]*)\)', t)
    if m:
        parts = [p for p in re.split(r'[\s,/]+', m.group(1).strip()) if p]
        if len(parts) < 3:
            return None
        def ch(p):
            return float(p[:-1]) * 2.55 if p.endswith('%') else float(p)
        r, g, b = (ch(p) for p in parts[:3])
        a = parts[3] if len(parts) > 3 else '1'
        a = float(a[:-1]) / 100 if a.endswith('%') else float(a)
        return (r, g, b, a)
    return None


def fmt(r, g, b, a):
    r, g, b = (max(0, min(255, round(x))) for x in (r, g, b))
    if a >= 0.999:
        return '#%02x%02x%02x' % (r, g, b)
    return 'rgba(%d, %d, %d, %s)' % (r, g, b, ('%.2f' % a).rstrip('0').rstrip('.'))


def hls(c):
    r, g, b, a = c
    h, l, s = colorsys.rgb_to_hls(r / 255, g / 255, b / 255)
    return h, l, s, a


def from_hls(h, l, s, a):
    r, g, b = colorsys.hls_to_rgb(h, max(0, min(1, l)), max(0, min(1, s)))
    return fmt(r * 255, g * 255, b * 255, a)


NAVY_HUE = 0.60  # dark surfaces lean navy, matching the RakanKampus brand


def dark_color(c, role):
    """Return the dark-mode replacement for colour c (or None to keep it)."""
    h, l, s, a = hls(c)
    tinted = s > 0.25 and (l < 0.955 or s > 0.6)  # soft off-whites like #f0fafa count as neutral
    if role == 'bg':
        if l >= 0.80:
            if a <= 0.35 and l > 0.97:
                return None  # faint white highlight on a coloured header — fine as is
            if tinted:
                # soft coloured chips / hover tints → dim version of the same hue, a bit raised
                return from_hls(h, min(0.26, 0.16 + (0.97 - l) * 0.6), 0.35, a)
            # neutral surfaces: white cards become the raised surface, off-white page backgrounds sink below them
            return from_hls(NAVY_HUE, max(0.075, 0.135 - (1 - l) * 1.1), 0.32, a)
        return None
    if role == 'text':
        if l >= 0.75:
            return None
        if s > 0.45 and l > 0.2:
            return from_hls(h, min(0.72, l + 0.24), s, a)  # keep accents, just brighter
        hh, ss = (h, min(s, 0.35) * 0.6) if s > 0.15 else (NAVY_HUE, 0.18)
        if l <= 0.45:
            return from_hls(hh, 0.90 - l * 0.35, ss, a)
        return from_hls(hh, min(0.74, l + 0.16), ss, a)
    if role == 'border':
        if l >= 0.72:
            if tinted:
                return from_hls(h, 0.22, 0.28, a)
            return from_hls(NAVY_HUE, 0.21, 0.22, a)
        return None
    if role == 'shadow':
        if l >= 0.8:
            return None
        return fmt(0, 0, 0, min(1.0, a * 1.8 + 0.05))
    return None


COLOR_TOKEN = re.compile(r'#[0-9a-fA-F]{3,8}\b|rgba?\([^)]*\)|\b(?:white|black)\b')


def dim(c):
    h, l, s, a = hls(c)
    if l >= 0.80:
        return dark_color(c, 'bg')
    return from_hls(h, l * 0.55, s * 0.9, a)


def map_value(value, role):
    changed = False
    if role == 'page-bg':  # the animated brand gradient behind pages: keep it, just much darker
        if 'gradient' not in value:
            role = 'bg'
        else:
            def subd(m):
                c = parse_color(m.group(0))
                return dim(c) if c else m.group(0)
            return COLOR_TOKEN.sub(subd, value)

    def sub(m):
        nonlocal changed
        c = parse_color(m.group(0))
        if not c:
            return m.group(0)
        d = dark_color(c, role)
        if d is None:
            return m.group(0)
        changed = True
        return d

    out = COLOR_TOKEN.sub(sub, value)
    return out if changed else None


def role_for(prop, value=''):
    p = prop.lower()
    if p in ('background', 'background-color', 'background-image'):
        return 'bg'
    if p in ('color', 'fill', 'stroke', 'caret-color', '-webkit-text-fill-color', 'text-decoration-color'):
        return 'text'
    if p.startswith('border') or p.startswith('outline') or p == 'column-rule':
        return 'border'
    if p in ('box-shadow', 'text-shadow'):
        return 'shadow'
    if p.startswith('--'):
        return None  # variables are resolved where they're used (see resolve_vars)
        n = p.lower()
        if any(k in n for k in ('text', 'ink', 'muted', 'faint', 'title', 'fg', 'label', 'placeholder')):
            return 'text'
        if any(k in n for k in ('border', 'line', 'divider', 'ring')):
            return 'border'
        if any(k in n for k in ('bg', 'card', 'surface', 'white', 'input', 'page', 'bubble', 'soft', 'panel', 'tint')):
            return 'bg'
        c = next((parse_color(m.group(0)) for m in COLOR_TOKEN.finditer(value)), None)
        if c and hls(c)[1] >= 0.8:
            return 'bg'
        return None  # brand/accent/navy variables stay
    return None

# ---------------------------------------------------------------- CSS

def scope_selector(sel):
    sel = sel.strip()
    if not sel:
        return None
    m = re.match(r'^(:root|html)\b(.*)$', sel)
    if m:
        return DARK + m.group(2)
    return DARK + ' ' + sel


VARS = {}
VAR_RE = re.compile(r'var\(\s*(--[\w-]+)\s*(?:,\s*([^)]*))?\)')


def collect_vars(rules):
    for rule in rules:
        if rule.type == 'qualified-rule':
            prelude = tinycss2.serialize(rule.prelude).strip()
            if prelude in (':root', 'html', 'body', ':root, body', 'html, body'):
                for d in tinycss2.parse_declaration_list(rule.content, skip_comments=True, skip_whitespace=True):
                    if d.type == 'declaration' and d.name.startswith('--'):
                        VARS[d.name] = tinycss2.serialize(d.value).strip()


def resolve_vars(value):
    for _ in range(3):
        nv = VAR_RE.sub(lambda m: VARS.get(m.group(1), m.group(2) or m.group(0)), value)
        if nv == value:
            break
        value = nv
    return value


def process_rules(rules, out, indent=''):
    for rule in rules:
        if rule.type == 'qualified-rule':
            prelude = tinycss2.serialize(rule.prelude).strip()
            if SKIP_SELECTORS.search(prelude):
                continue
            decls = tinycss2.parse_declaration_list(rule.content, skip_comments=True, skip_whitespace=True)
            new = []
            for d in decls:
                if d.type != 'declaration':
                    continue
                value = resolve_vars(tinycss2.serialize(d.value).strip())
                role = role_for(d.name, value)
                if role == 'bg' and prelude.strip() in ('body', 'html, body', 'body, html'):
                    role = 'page-bg'
                if not role:
                    continue
                mapped = map_value(value, role)
                if mapped:
                    name = d.name
                    if role == 'page-bg' and 'gradient' in value and name == 'background':
                        name = 'background-image'  # keep the page's background-size / animation
                    new.append('%s: %s%s;' % (name, mapped, ' !important' if d.important else ''))
            if new:
                sels = [scope_selector(s) for s in split_selectors(prelude)]
                sels = [s for s in sels if s]
                if sels:
                    out.append('%s%s { %s }' % (indent, ', '.join(sels), ' '.join(new)))
        elif rule.type == 'at-rule' and rule.lower_at_keyword in ('media', 'supports') and rule.content:
            inner = []
            process_rules(tinycss2.parse_rule_list(rule.content, skip_comments=True, skip_whitespace=True), inner, indent + '  ')
            if inner:
                out.append('%s@%s %s {' % (indent, rule.at_keyword, tinycss2.serialize(rule.prelude).strip()))
                out.extend(inner)
                out.append(indent + '}')


def split_selectors(prelude):
    parts, depth, cur = [], 0, ''
    for ch in prelude:
        if ch in '([':
            depth += 1
        elif ch in ')]':
            depth -= 1
        if ch == ',' and depth == 0:
            parts.append(cur)
            cur = ''
        else:
            cur += ch
    parts.append(cur)
    return parts


def page_css(src):
    src = BLOCK_RE.sub('\n', src)
    blocks = re.findall(r'<style(?:\s[^>]*)?>(.*?)</style>', src, re.S)
    out = []
    VARS.clear()
    blocks = [re.sub(r'\{\{.*?\}\}', '0', css) for css in blocks]  # blade echo inside CSS
    for css in blocks:
        collect_vars(tinycss2.parse_stylesheet(css, skip_comments=True, skip_whitespace=True))
    for css in blocks:
        process_rules(tinycss2.parse_stylesheet(css, skip_comments=True, skip_whitespace=True), out)
    return out

# ---------------------------------------------------------------- Tailwind

TW_CLASS = re.compile(r'(?<![\w:-])((?:[a-z-]+:)*(bg|text|border|divide|from|via|to|ring|placeholder)-(white|black|[a-z]+-\d{2,3})(?:/(\d{1,3}))?)(?![\w-])')
TW_PSEUDO = {'hover': ':hover', 'focus': ':focus', 'active': ':active', 'disabled': ':disabled', 'focus-within': ':focus-within', 'checked': ':checked'}
TW_SCREENS = {'sm': 640, 'md': 768, 'lg': 1024, 'xl': 1280}


def tailwind_palette():
    with open(os.path.join(os.path.dirname(__file__), 'tailwind-colors.json')) as f:
        pal = json.load(f)
    # Custom "indigo" (teal/navy) from the pages' tailwind.config
    pal['indigo'] = {'50': '#e6fbfa', '100': '#dbeeee', '200': '#b8e6e6', '300': '#94a3b8', '400': '#64748b',
                     '500': '#0d9488', '600': '#0d9488', '700': '#0f766e', '900': '#14213d'}
    return pal


def tw_escape(cls):
    return re.sub(r'([:/.\[\]])', r'\\\1', cls)


def tailwind_css(sources):
    pal = tailwind_palette()
    found = {}
    for src in sources:
        for m in TW_CLASS.finditer(src):
            found[m.group(1)] = m.groups()[1:]
    out = []
    for cls in sorted(found):
        util, color, alpha = found[cls]
        if color in ('white', 'black'):
            hexv = pal[color]
        else:
            name, shade = color.rsplit('-', 1)
            hexv = pal.get(name, {}).get(shade)
        if not hexv:
            continue
        c = parse_color(hexv)
        if alpha:
            c = (c[0], c[1], c[2], int(alpha) / 100)
        role = {'bg': 'bg', 'from': 'bg', 'via': 'bg', 'to': 'bg', 'text': 'text', 'placeholder': 'text',
                'border': 'border', 'divide': 'border', 'ring': 'border'}[util]
        d = dark_color(c, role)
        if not d:
            continue
        variants = cls.split(':')[:-1]
        sel, prefix, media, ok = '.' + tw_escape(cls), '', None, True
        for v in variants:
            if v in TW_PSEUDO:
                sel += TW_PSEUDO[v]
            elif v == 'group-hover':
                prefix = '.group:hover '
            elif v in TW_SCREENS:
                media = TW_SCREENS[v]
            else:
                ok = False
        if not ok:
            continue
        sel = prefix + sel
        if util == 'bg':
            decl = 'background-color: %s' % d
        elif util == 'text':
            decl = 'color: %s' % d
        elif util == 'placeholder':
            sel += '::placeholder'
            decl = 'color: %s' % d
        elif util == 'border':
            decl = 'border-color: %s' % d
        elif util == 'divide':
            sel += ' > :not([hidden]) ~ :not([hidden])'
            decl = 'border-color: %s' % d
        elif util == 'ring':
            decl = '--tw-ring-color: %s' % d
        elif util == 'from':
            decl = '--tw-gradient-from: %s; --tw-gradient-to: transparent; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)' % d
        elif util == 'via':
            decl = '--tw-gradient-stops: var(--tw-gradient-from), %s, var(--tw-gradient-to)' % d
        else:  # to
            decl = '--tw-gradient-to: %s' % d
        rule = '%s %s { %s !important; }' % (DARK, sel, decl)
        out.append('@media (min-width: %dpx) { %s }' % (media, rule) if media else rule)
    return out

# ---------------------------------------------------------------- main

def main():
    sources = []
    for rel in PAGES:
        path = os.path.join(VIEWS, rel)
        if not os.path.exists(path):
            print('skip (missing):', rel)
            continue
        src = open(path, encoding='utf-8').read()
        sources.append(src)
        rules = page_css(src)
        clean = BLOCK_RE.sub('\n', src)
        if not rules:
            if clean != src:
                open(path, 'w', encoding='utf-8').write(clean)
            continue
        block = ('<style id="rk-dark-theme">\n/* Dark theme — generated by scripts/dark-theme/generate.py, do not edit by hand.\n'
                 '   Hand-made fixes go in resources/views/partials/dark-fixes.blade.php */\n'
                 + '\n'.join(rules) + '\n</style>\n')
        if '</head>' in clean:
            new = clean.replace('</head>', block + '</head>', 1)
        else:  # partial without <head>: put it right after its own style block
            idx = clean.rfind('</style>')
            new = clean[:idx + 8] + '\n' + block + '\n' + clean[idx + 8:].lstrip('\n')
        open(path, 'w', encoding='utf-8').write(new)
        print('%-45s %4d rules' % (rel, len(rules)))

    tw = tailwind_css(sources)
    tw_path = os.path.join(VIEWS, 'partials', 'dark-tailwind.blade.php')
    with open(tw_path, 'w', encoding='utf-8') as f:
        f.write('{{-- Dark theme for Tailwind colour classes — generated by scripts/dark-theme/generate.py, do not edit by hand. --}}\n')
        f.write('<style id="rk-dark-tailwind">\n' + '\n'.join(tw) + '\n</style>\n')
    print('%-45s %4d rules' % ('partials/dark-tailwind.blade.php', len(tw)))


if __name__ == '__main__':
    sys.exit(main())
