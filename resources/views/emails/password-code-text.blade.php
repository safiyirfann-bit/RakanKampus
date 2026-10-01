Hi {{ $name }},

@if (($purpose ?? 'reset') === 'register')
Welcome to RakanKampus! Your verification code is: {{ $code }}

It expires in {{ $minutes }} minutes. If you didn't sign up, you can ignore this email — no account will be created.
@else
Your RakanKampus password reset code is: {{ $code }}

It expires in {{ $minutes }} minutes. If you didn't ask to reset your password, you can ignore this email — your password stays the same.
@endif

RakanKampus · Politeknik Ungku Omar
