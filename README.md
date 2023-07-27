# XWindow | 轻量级全功能MVC PHP框架
A lightweight, multifunctional PHP MVC framework

---

## 预览
![QQ截图20230726150803](https://github.com/Gingmzmzx/XWindow/assets/49107602/a668a0d2-cfd7-45e6-a05e-5864ec64a649)
![QQ截图20230726150723](https://github.com/Gingmzmzx/XWindow/assets/49107602/c77043ce-9aa1-4fb2-9cdf-2a76a2753526)
![QQ截图20230726151651](https://github.com/Gingmzmzx/XWindow/assets/49107602/e2c3ce2e-ba52-4c7f-ae90-bb87d051addc)

## 特性
- 界面基于MDUI搭建，清爽优雅，Windows风格，可以切换Tab
- lib库丰富，MVC架构，快速上手搭建，方便易用
- 轻量级框架，体积小，开箱即用
- 极速处理，响应速度快

## 使用

### 先决条件
本项目为轻量级的MVC框架，要求：
- PHP版本7.x（测试环境7.2）
- NGINX或APACHE配置伪静态：（此处提供NGINX规则）
  ```
  location /{
  	if (!-e $request_filename) {
  	  rewrite  ^(.*)$  /index.php/$1  last;
  	  break;
  	}
  }
  ```
### 构建项目
- 克隆本项目
- 将含有`init.php`的目录`XWindow`复制到您的项目根目录中
- 编辑`__DIR__/XWindow/config/base.php`中的配置
- 在您项目根目录下的`index.php`中添加
  ```PHP
  require __DIR__ . "/XWindow/init.php";
  ```
- 然后可以参考[文档](/docs)进行路由Route

### 更新
只需要将最新的XWindow文件夹替换即可。

## 开发文档
**见[文档](/docs)**

## 开源协议
本项目基于`Apache License 2.0` [http://www.apache.org/licenses/](http://www.apache.org/licenses/)  
请自觉遵守开源协议，谢谢！