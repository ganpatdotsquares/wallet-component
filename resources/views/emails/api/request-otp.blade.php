<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>OTP Verification</title>
  </head>
  <body style="margin: 0; background: #f8f8f8;">
    <div style="background: #f8f8f8; font-family: Arial, sans-serif; line-height: 28px; height: 100%; width: 100%; color: #514d6a;">
      <div style="max-width: 700px; padding: 50px 0; margin: 0 auto; font-size: 14px;">
      <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px;">
          <tbody>
            <tr>
              <td style="vertical-align: top; padding-bottom: 30px;" align="center">
                <a href="{{ env('APP_URL', 'http://localhost') }}" target="_blank">
                  <img src="{{ asset('images/logo.png') }}" alt="{{config('GET.system_application_name')}}" style="border: none;" />
                </a>
              </td>
            </tr>
          </tbody>
        </table>
        @isset($content)
          {!! $content !!}
        @endisset
      </div>
    </div>
  </body>
</html>
