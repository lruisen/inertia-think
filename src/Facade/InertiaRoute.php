<?php

namespace Inertia\Facade;

use Closure;
use Inertia\InertiaRoute as InertiaRouteFactory;
use think\Facade;
use think\Request;
use think\Response;
use think\Route;
use think\route\Dispatch;
use think\route\Domain;
use think\route\Rule;
use think\route\RuleGroup;
use think\route\RuleItem;
use think\route\RuleName;
use think\route\Url as UrlBuild;


/**
 * @see Route
 * @package Inertia\Facade
 * @mixin Route
 * @method static mixed config(string $name = null)
 * @method static Route lazy(bool $lazy = true) 设置路由域名及分组（包括资源路由）是否延迟解析
 * @method static Route mergeRuleRegex(bool $merge = true) 设置路由域名及分组（包括资源路由）是否合并解析
 * @method static void setGroup(RuleGroup $group) 设置当前分组
 * @method static RuleGroup getGroup(string $name = null) 获取指定标识的路由分组 不指定则获取当前分组
 * @method static Route pattern(array $pattern) 注册变量规则
 * @method static Route option(array $option) 注册路由参数
 * @method static Domain domain(string|array $name, mixed $rule = null) 注册域名路由
 * @method static array getDomains() 获取域名
 * @method static RuleName getRuleName() 获取RuleName对象
 * @method static Route bind(string $bind, string $domain = null) 设置路由绑定
 * @method static array getBind() 读取路由绑定信息
 * @method static string|null getDomainBind(string $domain = null) 读取路由绑定
 * @method static RuleItem[] getName(string $name = null, string $domain = null, string $method = '*') 读取路由标识
 * @method static void import(array $name) 批量导入路由标识
 * @method static void setName(string $name, RuleItem $ruleItem, bool $first = false) 注册路由标识
 * @method static void setRule(string $rule, RuleItem $ruleItem = null) 保存路由规则
 * @method static RuleItem[] getRule(string $rule) 读取路由
 * @method static array getRuleList() 读取路由列表
 * @method static void clear() 清空路由规则
 * @method static RuleItem rule(string $rule, mixed $route = null, string $method = '*') 注册路由规则
 * @method static Route setCrossDomainRule(Rule $rule) 设置跨域有效路由规则
 * @method static RuleGroup group(string|Closure $name, mixed $route = null) 注册路由分组
 * @method static RuleItem any(string $rule, mixed $route) 注册路由
 * @method static RuleItem get(string $rule, mixed $route) 注册GET路由
 * @method static RuleItem post(string $rule, mixed $route) 注册POST路由
 * @method static RuleItem put(string $rule, mixed $route) 注册PUT路由
 * @method static RuleItem delete(string $rule, mixed $route) 注册DELETE路由
 * @method static RuleItem patch(string $rule, mixed $route) 注册PATCH路由
 * @method static RuleItem head(string $rule, mixed $route) 注册HEAD路由
 * @method static RuleItem options(string $rule, mixed $route) 注册OPTIONS路由
 * @method static Resource resource(string $rule, string $route) 注册资源路由
 * @method static RuleItem view(string $rule, string $template = '', array $vars = []) 注册视图路由
 * @method static RuleItem redirect(string $rule, string $route = '', int $status = 301) 注册重定向路由
 * @method static Route rest(string|array $name, array|bool $resource = []) rest方法定义和修改
 * @method static array|null getRest(string $name = null) 获取rest方法定义的参数
 * @method static RuleItem miss(string|Closure $route, string $method = '*') 注册未匹配路由规则后的处理
 * @method static Response dispatch(Request $request, Closure|bool $withRoute = true) 路由调度
 * @method static Dispatch|false check() 检测URL路由
 * @method static Dispatch url(string $url) 默认URL解析
 * @method static UrlBuild buildUrl(string $url = '', array $vars = []) URL生成 支持路由反射
 * @method static RuleGroup __call(string $method, array $args) 设置全局的路由分组参数
 * @method static RuleItem inertia(string $uri, string $component, array $props = []) 设置Inertia路由
 */
class InertiaRoute extends Facade
{
    public static function getFacadeClass(): string
    {
        return InertiaRouteFactory::class;
    }
}
