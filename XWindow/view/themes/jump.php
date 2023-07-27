<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no" />
    <title>外部链接<?php e(' - '.config('site_name')); ?></title>
    <link rel="shortcut icon" href="/statics/images/head.jpg">
    <meta name="referrer" content="never">

    <!-- MDUI -->
    <link rel="stylesheet" href="//unpkg.com/mdui@1.0.2/dist/css/mdui.min.css" />
    <script src="//cdn.xzynb.top/mdui/js/mdui.min.js"></script>

    <!-- AdsByGoogle -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3080095736338495" crossorigin="anonymous"></script>

    <style>
        body {
            background-color: #F7F6F5;
        }
        
        .fuild {
            width: 100vw;
            height: 100vh;
        }
    </style>
</head>
<body class="fuild">
    <div class="mdui-container mdui-valign fuild">
        <div class="mdui-center">
            <div class="mdui-card mdui-hoverable">
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">确定要前往吗</div>
                    <div class="mdui-card-primary-subtitle">即将要前往<a href="<?php echo $_GET['url']; ?>"><?php echo $_GET['url']; ?></a>，该页面不是本站官方页面，请注意保护您的安全与隐私。</div>
                </div>
                <div class="mdui-card-actions">
                    <button class="mdui-btn mdui-ripple" onclick="window.location.href = '<?php echo $_GET['url']; ?>'">前往</button>
                    <button class="mdui-btn mdui-ripple" onclick="window.location.href = '/'">返回主页</button>
                    <button class="mdui-btn mdui-ripple" onclick="window.close()">关闭页面</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>