<?php


namespace Inertia;

use think\template\TagLib;

class InertiaTag extends TagLib
{
    protected $tags = [
        'inertia' => ['attr' => '', 'close' => 0], // 非必须属性：id
        'inertiahead' => ['close' => 0],
    ];

    public function tagInertia($tag, $content): string
    {
        $id = !empty($tag['id']) ? trim(trim($tag['id']), "\'\"") : 'app';

        $parse = <<<parse
<?php
    if (!isset(\$__inertiaSsrDispatched)) {
        \$__inertiaSsrDispatched = true;
        \$__inertiaSsrResponse = app(\Inertia\Ssr\Gateway::class)->dispatch(\$page);
    }

    if (\$__inertiaSsrResponse) {
        echo \$__inertiaSsrResponse->body;
    } else {
?>
       <div id="{$id}" data-page="<?php json_encode(\$page)} ?>"></div>
<?php
}
?>
parse;
        
        return $parse;
    }

    public function tagInertiaHead($tag, $content): string
    {
        $parse = '<?php
            if (!isset($__inertiaSsrDispatched)) {
                $__inertiaSsrDispatched = true;
                $__inertiaSsrResponse = app(\Inertia\Ssr\Gateway::class)->dispatch($page);
            }

            if ($__inertiaSsrResponse) {
                echo $__inertiaSsrResponse->head;
            }
        ?>';

        return implode(' ', array_map('trim', explode("\n", $parse)));
    }
}
