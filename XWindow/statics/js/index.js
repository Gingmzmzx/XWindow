var $$ =mdui.$;

        class WindowTools {
            static clientHeight = document.body.clientHeight;
            static defaultUrl = "/home";
            static defaultTitle = "首页";
            static defaultIcon = "home";
            static scriptList = [];

            static axios = class{
                static buildOptions(options){
                    if (options.headers == undefined) {
                        options.headers = {};
                    }
                    options.headers['XWindow-Client'] = 'XWindowRequest/0.1.0';
                    return options;
                }

                static processRes(res, func) {
                    if (func == undefined) {
                        return res;
                    } else {
                        res.then(func);
                    }
                }

                static get(url, func = undefined, options = {}){
                    options = WindowTools.axios.buildOptions(options);
                    let res = axios.get(url, options);
                    return WindowTools.axios.processRes(res, func);
                }
                
                static post(url, func = undefined, options = new FormData()){
                    options.append("XWindow-Client", 'XWindowRequest/0.1.0');
                    let res = axios.post(url, options);
                    return WindowTools.axios.processRes(res, func);
                }
            }

            static renderDialog(name, inst, ele = undefined, forcs = false, prefix = ""){
                if (ele == undefined){
                    ele = `#${name}Dialog`;
                }
                ele = $(ele).children(".mdui-dialog-content");
                if (ele.children().length - ele.children("#dialogLoading").length <= 0){
                    ele.html(`
                    <div style="width:100%;height:100%;" class="mdui-valign" id="dialogLoading">
                        <div class="mdui-spinner mdui-spinner-colorful mdui-center"></div>
                    </div>
                    `);
                    $$(ele).mutation();
                    inst.toggle();

                    WindowTools.axios.get(`${prefix}/dialog/${name}`, function(res){
                        if (ele.children().length - ele.children("#dialogLoading").length <= 0){
                            ele.html(res.data);
                            $$(ele).mutation();
                            inst.handleUpdate();
                        }
                    });
                }else {
                    inst.toggle();
                }
            }

            static loadScript(name, prefix=""){
                if (!WindowTools.scriptList[name]){
                    WindowTools.scriptList[name] = true;
                    $("body").prepend(`<script type='text/javascript' src='${prefix}/statics/js/tab/${name}.js?v=${new Date()}'></script>`);
                    return true;
                }
                return false;
            }

            static loadScriptWithoutCache(name){
                if (!WindowTools.scriptList[name]){
                    TLWindow.startProgress();
                    WindowTools.scriptList[name] = true;
                    $.ajax({
                        method: 'GET',
                        async: false,
                        url: `/statics/js/tab/${name}.js?v=${new Date()}`,
                        success: function (data) {
                            TLWindow.stopProgress();
                            $("body").prepend(`<script type='text/javascript'>${data}</script>`);
                        }
                    });
                    return true;
                }
                return false;
            }

            static loadPage(){
                $("#layout-warning").hide();
                $("body").height(WindowTools.clientHeight - $(".mdui-bottom-nav").height());
                $("#main_content").height($("body").height());
            }

            static onMobile(func){
                var ua = navigator.userAgent;
                var ipad = ua.match(/(iPad).*OS\s([\d_]+)/),
                isIphone = !ipad && ua.match(/(iPhone\sOS)\s([\d_]+)/),
                isAndroid = ua.match(/(Android)\s+([\d.]+)/),
                isMobile = isIphone || isAndroid;
                if (isMobile) {
                    func();
                }
            }
            
            static onDesktop(func){
                var ua = navigator.userAgent;
                var ipad = ua.match(/(iPad).*OS\s([\d_]+)/),
                isIphone = !ipad && ua.match(/(iPhone\sOS)\s([\d_]+)/),
                isAndroid = ua.match(/(Android)\s+([\d.]+)/),
                isMobile = isIphone || isAndroid;
                if (!isMobile) {
                    func();
                }
            }

            static renderCap(ele = "captcha") {
                return hcaptcha.render(ele, { sitekey: 'ab3362da-eb6b-43f5-99e8-4eab2873cdaf' });
            }
        }

        class TabWindow {
            constructor(tabEleId = "#window_tab", contentEleId = "#main_content") {
                this.tabElement = $(tabEleId);
                this.tabEleId = tabEleId;
                this.contentEleId = contentEleId;
                this.tabCount = 0;
                this.contentElement = $(contentEleId);
                this.inst = new mdui.Tab(tabEleId);
                this.deletedTabCount = 0;
                this.deletedTab = [];
                this.clearFlag = true;
            }

            refresh() {
                return this.inst.handleUpdate();
            }

            openTab(url = WindowTools.defaultUrl, title = WindowTools.defaultTitle, icon = WindowTools.defaultIcon){
                this.startProgress();
                if (this.clearFlag) {
                    $("#main_content").empty();
                    this.clearFlag = false;
                }
                this.tabElement.append(`
                <a id="window-tab-card-${this.tabCount}" data-id="${this.tabCount}" data-url="${url}" href="#window-tab${this.tabCount}" class="mdui-ripple mdui-ripple-white">
                    <i class="mdui-icon material-icons">${icon}</i>
                    <label>${title}</label>
                </a>
                `);
                this.contentElement.append(`
                <div id="window-tab${this.tabCount}" class="window-tab"></div>
                `);
                var id = `#window-tab${this.tabCount}`,
                    stopProgress = this.stopProgress;
                WindowTools.axios.get(url, function(res) {
                    $(id).html(res.data);
                    WindowTools.loadPage();
                    stopProgress();
                });
                this.refresh();
                this.inst.show(this.tabCount - this.deletedTabCount);
                return this.tabCount ++;
            }

            closeActiveTab(){
                var ele = this.tabElement.children(".mdui-tab-active"),
                    tabCount = ele.attr("data-id");
                ele.remove();
                this.contentElement.children(`#window-tab${tabCount}`).remove();
                this.deletedTabCount ++;
                this.refresh();
                this.deletedTab[tabCount] = true;

                // Choose the last tab or show the tip.
                for (; tabCount >= 0; tabCount--) {
                    if (this.deletedTab[tabCount] != true){
                        break;
                    }
                }
                if (tabCount <= 0){
                    if (this.contentElement.children().length > 0){
                        this.inst.show(0);
                    } else {
                        this.contentElement.html(`
                        <div class="mdui-valign" style="width:100%;height:100%;">
                            <p class="mdui-center mdui-typo-display-1">请点击右下方按钮打开新的窗口</p>
                        </div>
                        `);
                        this.clearFlag = true;
                    }
                }else {
                    var deletedCount = 0;
                    for (var i = 0; i < tabCount; i++) {
                        if (this.deletedTab[i] == true){
                            deletedCount ++;
                        }
                    }
                    this.inst.show(tabCount - deletedCount);
                }
            }

            changeTab(id, url = undefined, title = undefined, icon = undefined){
                this.startProgress();
                element = $(`#window-tab-card-${id}`);
                if (title !== undefined) {
                    element.children("label").text(title);
                }
                if (icon !== undefined) {
                    element.children("i").text(icon);
                }
                if (url !== undefined) {
                    element.attr("url", url);
                }
                this.stopProgress();
                return this.refresh();
            }

            stopProgress(){
                $('#window-progress').addClass('mdui-hidden');
                $$.hideOverlay();
                $$.unlockScreen();
            }

            startProgress(){
                $('#window-progress').removeClass('mdui-hidden');
                $$.showOverlay();
                $$.lockScreen();
            }
        }
