class MainWindow {
    constructor(){
        this.settingsInst = new mdui.Dialog("#settingsDialog", {
            history: false
        });
    }
    
    openSettings(){
        WindowTools.renderDialog("settings", this.settingsInst, undefined, false, "/XWindow/api");
    }

    saveSettings(){
        this.settingsInst.close();
    }
}

var mainWindow = new MainWindow();

$(function() {
    WindowTools.loadPage();
    TLWindow.stopProgress();
    
    if ($.cookie("capVerify") == true) {
        mdui.snackbar('未通过hCaptcha验证，请重试！');
        $.cookie("capVerify", false);
    }
    
    $(window).on("resize", function(){
        WindowTools.loadPage();
    });

    var OriginTitile = document.title, titleTime;
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            document.title = "待机中 | " + OriginTitile;
        } else {
            document.title = OriginTitile;
        }
    });

    /*
    WindowTools.onMobile(function(){
        $("body").html(`
        <div class="mdui-card mdui-m-a-1">
            <div class="mdui-card-primary">
                <div class="mdui-card-primary-title">请使用电脑访问</div>
                <div class="mdui-card-primary-subtitle">未兼容手机端</div>
            </div>
            <div class="mdui-card-content">请使用电脑端访问本站，本站目前未进行手机端适配，请谅解！</div>
        </div>
        `);
    });
    */
});