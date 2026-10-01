<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Your RakanKampus code</title></head>
<body style="margin:0;padding:0;background:#f3eefe;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1e1b2e">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3eefe;padding:32px 12px">
    <tr><td align="center">
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 12px 30px rgba(90,60,160,.12)">
        <tr><td style="background:linear-gradient(120deg,#a78bfa,#f472b6);background-color:#b38cf6;padding:28px 28px 24px;text-align:center">
          <div style="font-size:30px;line-height:1">🤖</div>
          <div style="color:#ffffff;font-size:20px;font-weight:800;margin-top:8px">RakanKampus</div>
          <div style="color:#fbe7ff;font-size:13px;margin-top:2px">Your Politeknik AI Assistant</div>
        </td></tr>
        <tr><td style="padding:28px 30px 8px">
          <p style="margin:0 0 6px;font-size:16px;font-weight:700">Hi {{ $name }},</p>
          <p style="margin:0;font-size:14.5px;line-height:1.6;color:#4b4763">{{ ($purpose ?? 'reset') === 'register' ? 'Welcome to RakanKampus! Use this code to confirm your email and finish creating your account:' : 'Use this code to reset your RakanKampus password:' }}</p>
        </td></tr>
        <tr><td align="center" style="padding:18px 30px">
          <div style="display:inline-block;background:#f6f2ff;border:2px dashed #c4b5fd;border-radius:18px;padding:16px 26px;font-size:34px;font-weight:800;letter-spacing:10px;color:#5b21b6;font-family:'SFMono-Regular',Menlo,Consolas,monospace">{{ $code }}</div>
          <p style="margin:12px 0 0;font-size:13px;color:#7c7896">Expires in {{ $minutes }} minutes</p>
        </td></tr>
        <tr><td style="padding:8px 30px 28px">
          <p style="margin:0;font-size:13px;line-height:1.6;color:#7c7896">@if (($purpose ?? 'reset') === 'register')Didn't sign up for RakanKampus? You can ignore this email — no account will be created.@else Didn't ask to reset your password? You can ignore this email — your password stays the same.@endif Never share this code with anyone.</p>
        </td></tr>
        <tr><td style="background:#faf8ff;padding:16px 30px;text-align:center;font-size:12px;color:#9a96b3">RakanKampus · Politeknik Ungku Omar</td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
