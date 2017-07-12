<!DOCTYPE html>
<html>
<head>
    <title>金地物业资源配置标准测算系统</title>
    <script type="text/javascript" src="http://112.74.80.132:8080/administrator/js/jquery.min.js"></script>
    <script type="text/javascript" src="http://112.74.80.132:8080/administrator/js/jquery.fullbg.js"></script>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "微软雅黑", Arial, sans-serif;
            font-size: 14px;
            color: #fff;
            height: 100%;
            overflow: hidden;
        }

        a {
            color: #fff;
            text-decoration: none;
        }

        td {
            margin: 0;
            padding: 0;
        }

        h2 {
            font-size: 32px;
            color: #fff;
            font-family: "黑体";
            font-weight: lighter;
        }
        /*********************************************************************************************************/
        .fullBg {
            position: absolute;
            top: 0;
            left: 0;
            z-index: -9;
        }

        #maincontent {
            position: relative;
            top: 0;
            right: 0;
            left: 0;
            z-index: 99;
            width: 100%;
            height: 100%;
        }
        /*********************************************************************************************************/
        .login_top {
            width: 100%;
            height: 200px;
            text-align: center;
        }

        .login_top_box {
            margin: 0 auto;
            width: 740px;
        }

        .login_mid {
            width: 100%;
            height: 333px;
            text-align: center;
            background: url(http://112.74.80.132:8080/administrator/images/index_box.png) center top no-repeat;
        }

        .login_mid_box {
            margin: 0 auto;
            width: 390px;
            padding-top: 207px;
            text-align: center;
        }

        .txtput {
            width: 150px;
            height: 36px;
            padding-left: 30px;
            line-height: 32px;
            font-size: 16px;
            color: #fff;
            border: 0;
            background: none;
        }

        .txtbtn {
            border: none;
            width: 118px;
            height: 68px;
            cursor: pointer;
            font-size: 14pt;
            font-weight: bold;
            color: #ffffff;
            background: url(http://112.74.80.132:8080/administrator/images/index_btn.png) no-repeat;
        }

        .login_foot {
            margin: 0 auto;
            width: 100%;
            text-align: center;
            font-size: 14px;
            color: #fff;
            position: fixed;
            left: 0;
            right: 0;
            bottom: 5px;
            z-index: 10;
        }

            .login_foot img {
                vertical-align: middle;
            }
        /*********************************************************************************************************/
    </style>
</head>

<body>
    <div id="login_bg"><img src="http://112.74.80.132:8080/administrator/images/login_bg.jpg" height="100%"></div>
    <div id="login_mian">
        <div id="mainbox">
            <div class="login_top">
                <div class="login_top_box">&nbsp;</div>
            </div>
            <div class="login_mid">
                <div class="login_mid_box">
                    <table border="0" align="center" cellpadding="0" cellspacing="0" width="390">
                        <tr>
                            <td height="80" align="center" width="50%">
                                <input name="" type="button" id="btlogin" tabindex="2" value="商业物业" class="txtbtn" onclick="location.href = '?/main/'" />
                            </td>
                            <td height="80" align="center" width="50%">
                                <input name="" type="button" id="btlogin" tabindex="3" value="住宅物业" class="txtbtn" onclick="location.href = 'http://112.74.80.132:8080/index.aspx'" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" height="30">&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="login_foot">
                <div class="login_foot_box"> <img src="http://112.74.80.132:8080/administrator/images/foot_logo.png" /> &nbsp; 地址：深圳市南山区高新南九道9号威新软件科技园7号楼3A层 系统维护：<a href="http://www.07551.com" title="深圳网络公司" target="_blank">深一互联</a></div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(window).load(function () {
            $("#login_bg").fullBg();
        });
    </script>
</body>
</html>