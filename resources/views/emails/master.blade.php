<!DOCTYPE>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title></title>
</head>

<body style="background:#f6f6f6; padding:0px; margin:0px;">
  <div style="width:900px">
    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #dddddd;">
      <tr>
        <td>
          <table width="100%" border="0" cellpadding="5" cellspacing="0" style="border: solid 1px #0b3f56;border-bottom: none;">
            <tr style="text-align: center;">
              <td><a href="#">
                  @if (config('GET.main_logo'))
                  <img src="{{ asset('assets/photo/' . config('GET.main_logo')) }}" alt="logo" style="width:15%">
                  @else
                  <img width="15%" src="{{ asset('backend/assets/images/dots.png') }}" alt="homepage" />
                  @endif
                </a></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <table width="100%" cellpadding="5" cellspacing="0" style="border: solid 1px #0b3f56;border-top: none;">
          <tbody>
            <tr>
              <td style="color: #000000; font-family:Arial, Helvetica, sans-serif; font-size: 14px;">{!! $content !!}</td>
            </tr>
          </tbody>
        </table>
      </tr>
      <tr>
        <td align="right">&nbsp;</td>
      </tr>
      <tr>
        <td>
          <table width="100%" cellpadding="0" cellspacing="0" style="padding-top: 20px;">
            <tr>
              <td style="background-color:#e5ebeb; color:#4a5a5a; font-size:12px; font-family:Arial, Helvetica, sans-serif; text-align:center; padding:10px;">
                Powered by <a href="{{url('/')}}">{{config('GET.system_application_name')}}</a>.
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </div>
</body>

</html>