<!DOCTYPE>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title></title>
</head>

<body style="background:#f6f6f6; padding:0px; margin:0px;">
  <div>
    <table width="600" cellpadding="0" cellspacing="0" align="center" bgcolor="#093D66" style="border:1px solid #dddddd;">
      <tr>
        <td>
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#093D66; border-bottom:solid 3px #0b5aa4; padding:20px; text-align:center;">
            <tr>
              <td align="center"><a href="#"><img src="{{ asset('images/logo.png') }}" /></a></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" style="border: solid 1px #0b3f56">
          <tbody>
            <tr>
              <td align="center" style="color: #000000; font-family:Arial, Helvetica, sans-serif; font-size: 14px;">{!! $content !!}</td>
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
              <td align="center" style="background-color:#e5ebeb; color:#4a5a5a; font-size:12px; font-family:Arial, Helvetica, sans-serif; text-align:center; padding:10px;">
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