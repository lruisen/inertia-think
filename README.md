# inertia-think

Inertia.js的ThinkPHP适配器（ThinkPHP 8）

访问[inertiajs.com](https://inertiajs.com/)了解更多信息。

### 安装依赖项

首先，使用Composer包管理器安装Inertia服务器端适配器。

~~~composer 
composer require lruisen/inertia-think
~~~

### 根模板

接下来，设置将在访问应用程序的第一个页面时加载的根模板。这将用于加载您的网站资源（CSS和JavaScript），还将包含一个根＜div＞，用于启动您的JavaScript应用程序。

~~~html
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
   <script type="module" src="http://[::1]:5173/@vite/client"></script>
   <script type="module" src="http://[::1]:5173/resources/js/app.js"></script>
</head>
<body>
<div id="app" data-page={:json_encode($page)}></div>
</body>
</html>
~~~

默认情况下，Inertia的ThinkPHP适配器将假定您的根模板名为`app.html`。如果您想使用不同的根视图，可以使用`Inertia::setRootView()`方法进行更改。

### 中间件

::: warning

将`Inertia\InertiaResponseMiddleware` 中间件加入到`app/middleware.php`文件中或您的指定应用中间件配置文件中。

:::

接下来，我们需要创建Inertia中间件。您可以通过以下command命令来完成将`HandleInertiaRequests`中间件发布到您的应用程序。

~~~shell
php think inertia:middleware
~~~

中间件发布后：

* 如果是单应用，将`HandleInertiaRequests`中间件附加到`app/middleware.php`文件中。

* 如果是多应用，将`HandleInertiaRequests`中间件添加到对应应用的`middleware.php`文件中。

### 创建响应

就这样，服务端配置基本完成！现在，您可以开始创建Inertia页面并通过响应进行渲染了。

~~~php
use Inertia\facade\Inertia;

class EventsController extends Controller
{
    public function show(Event $event)
    {
        return Inertia::render('Event/Show', [
            'event' => $event->only(
                'id',
                'title',
                'start_date',
                'description'
            ),
        ]);
    }
}
~~~

### 关于路由

如果您的页面不需要相应的控制器方法，如“FAQ”或“about”页面，则可以通过`InertiaRoute::inertia()`方法或使用闭包形式直接路由到组件。

~~~php
use Inertia\Facade\InertiaRoute;
use think\facade\Route;

InertiaRoute::inertia('/about','about');

# 或

Route::get('/about',fn () => inertia('about'));
~~~

