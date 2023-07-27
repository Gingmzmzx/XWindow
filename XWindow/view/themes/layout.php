<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no" />
    <title><?php e($title.' - '.config('site_name')); ?></title>
    <link rel="shortcut icon" href="/statics/images/head.jpg">
    <meta name="referrer" content="never">

    <!-- hCaptcha -->
    <script src="https://js.hcaptcha.com/1/api.js?render=explicit" async defer></script>
    <!-- MDUI -->
    <link rel="stylesheet" href="//unpkg.com/mdui@1.0.2/dist/css/mdui.min.css" />
    <script src="//cdn.xzynb.top/mdui/js/mdui.min.js"></script>
    <!-- MD5 -->
    <script src="//cdn.xzynb.top/js/md5.js"></script>
    <!-- MTU -->
    <link rel="stylesheet" href="//cdn.xzynb.top/css/mtu.min.css">
    <script src="//cdn.xzynb.top/js/mtu.min.js"></script>
    <!-- AdsByGoogle -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3080095736338495" crossorigin="anonymous"></script>
    <!-- MarkDown -->
    <script src="//cdn.xzynb.top/js/markdown.js"></script>
    <!-- AXIOS -->
    <script src="//cdn.xzynb.top/js/axios.min.js"></script>
    <!-- JQ and plugins -->
    <script src="//cdn.xzynb.top/js/jquery-3.5.1.min.js"></script>
    <script src="//cdn.xzynb.top/js/jquery.cookie.min.js"></script>
    <!-- ClipboardJS -->
    <script src="//cdn.xzynb.top/js/clipboard.min.js"></script>
    <!-- Custom -->
    <link rel="stylesheet" href="/XWindow/statics/css/index.css" />
</head>
<body class="mdui-theme-primary-indigo mdui-theme-accent-blue mdui-bottom-nav-fixed">
    <script src="/XWindow/statics/js/index.js?v=<?php echo time(); ?>"></script>

    <!-- Something. -->
    <div id="logo" class="mdui-ripple" onclick="mainWindow.openSettings()">
        <img class="mdui-img-circle mdui-img-fluid mdui-hoverable" src="/statics/images/head.jpg"/>
    </div>
    <div id="window-progress" class="mdui-progress">
        <div class="mdui-progress-indeterminate"></div>
    </div>
    <div class="mdui-fab-wrapper" mdui-fab="">
        <button class="mdui-fab mdui-ripple mdui-color-theme-accent" mdui-tooltip="{content: '更多选项', position: 'left'}">
            <i class="mdui-icon material-icons">more_horiz</i>
            <i class="mdui-icon mdui-fab-opened material-icons">keyboard_arrow_down</i>
        </button>
        <div class="mdui-fab-dial" style="height: 0px;">
            <button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-pink" style="transition-delay: 10ms;" mdui-tooltip="{content: '打开新窗口', position: 'left'}" onclick="TLWindow.openTab()">
                <i class="mdui-icon material-icons">add</i>
            </button>
            <button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-red" style="transition-delay: 110ms;" mdui-tooltip="{content: '关闭该窗口', position: 'left'}" onclick="TLWindow.closeActiveTab()">
                <i class="mdui-icon material-icons">close</i>
            </button>
            <button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-blue<?php if (!checkLogin($_COOKIE['user'], $_COOKIE['password'], false)){ ?> mdui-hidden<?php } ?>" style="transition-delay: 310ms;" id="fab-logout" mdui-tooltip="{content: '登出', position: 'left'}" onclick="TLWindow.openTab('/login/logout', '登录', 'fingerprint')">
                <i class="mdui-icon material-icons">&#xe8ac;</i>
            </button>
            <button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-blue<?php if (checkLogin($_COOKIE['user'], $_COOKIE['password'], false)){ ?> mdui-hidden<?php } ?>" style="transition-delay: 310ms;" id="fab-login" mdui-tooltip="{content: '登录', position: 'left'}" onclick="TLWindow.openTab('/login', '登录', 'fingerprint')">
                <i class="mdui-icon material-icons">fingerprint;</i>
            </button>
        </div>
    </div>

    <div class="mdui-dialog" id="settingsDialog">
        <div class="mdui-dialog-title">设置</div>
        <div class="mdui-dialog-content"></div>
        <div class="mdui-dialog-actions">
            <button class="mdui-btn mdui-ripple mdui-float-left" onclick="mainWindow.settingsInst.handleUpdate()">调整窗口大小</button>
            <button class="mdui-btn mdui-ripple" mdui-dialog-close>取消</button>
            <button class="mdui-btn mdui-ripple" onclick="mainWindow.saveSettings()">确定</button>
        </div>
    </div>

    <!-- Layout-main -->
    <div id="layout-main">
        <div id="main_content" class="mdui-container-fluid">
            <div class="mdui-valign" style="width:100%;height:100%;">
                <p class="mdui-center mdui-typo-display-1">请点击右下方按钮打开新的窗口</p>
            </div>
        </div>
        <div class="mdui-bottom-nav mdui-color-theme">
            <div class="mdui-tab mdui-tab-full-width mdui-color-theme" id="window_tab" mdui-tab></div>
        </div>
    </div>
    
    <div id="layout-warning" class="mdui-container">
        <div class="mdui-card">
            <div class="mdui-card-primary">
                <div class="mdui-card-primary-title">JS警告</div>
                <div class="mdui-card-primary-subtitle">请启用JavaScript</div>
            </div>
            <div class="mdui-card-content mdui-typo">
                <div class="mdui-typo-headline">本站需要使用JavaScript来完成页面的加载，请启用JS或更换支持JS的浏览器！</div>
            </div>
        </div>
    </div>
    <noscript>
        <style>
            #layout-main {
                display: none;
            }
            #layout-warning .mdui-card {
                background-color: yellow;
                color: red;
                text-align: center;
            }
        </style>
    </noscript>

    <script>
        var TLWindow = new TabWindow();

        WindowTools.loadScript("main", "/XWindow");
    </script>
</body>
</html>