<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Blade;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Directives
{

    public static function directives(): array
    {

        return [

            /*
            |---------------------------------------------------------------------
            | @istrue / @isfalse
            |---------------------------------------------------------------------
            |
            | These directives can be used in different ways.
            | @istrue($v) Echo this @endistrue, @istrue($v, 'Echo this')
            | or @istrue($variable, $echoThisVariables)
            |
            */

            'istrue' => function ($expression) {
                if (Str::contains($expression, ',')) {
                    $expression = self::multipleArgs($expression);

                    return implode('', [
                        "<?php if (isset({$expression->get(0)}) && (bool) {$expression->get(0)} === true) : ?>",
                        "<?php echo {$expression->get(1)}; ?>",
                        '<?php endif; ?>',
                    ]);
                }

                return "<?php if (isset({$expression}) && (bool) {$expression} === true) : ?>";
            },

            'endistrue' => function ($expression) {
                return '<?php endif; ?>';
            },

            'isfalse' => function ($expression) {
                if (Str::contains($expression, ',')) {
                    $expression = self::multipleArgs($expression);

                    return implode('', [
                        "<?php if (isset({$expression->get(0)}) && (bool) {$expression->get(0)} === false) : ?>",
                        "<?php echo {$expression->get(1)}; ?>",
                        '<?php endif; ?>',
                    ]);
                }

                return "<?php if (isset({$expression}) && (bool) {$expression} === false) : ?>";
            },

            'endisfalse' => function ($expression) {
                return '<?php endif; ?>';
            },

            /*
            |---------------------------------------------------------------------
            | @isnull / @isnotnull
            |---------------------------------------------------------------------
            |
            | These directives can be used in different ways.
            | @isnull($v) Echo this @endisnull, @isnull($v, 'Echo this')
            | or @isnull($variable, $echoThisVariables)
            |
            */

            'isnull' => function ($expression) {
                if (Str::contains($expression, ',')) {
                    $expression = self::multipleArgs($expression);

                    return implode('', [
                        "<?php if (is_null({$expression->get(0)})) : ?>",
                        "<?php echo {$expression->get(1)}; ?>",
                        '<?php endif; ?>',
                    ]);
                }

                return "<?php if (is_null({$expression})) : ?>";
            },

            'endisnull' => function ($expression) {
                return '<?php endif; ?>';
            },

            'isnotnull' => function ($expression) {
                if (Str::contains($expression, ',')) {
                    $expression = self::multipleArgs($expression);

                    return implode('', [
                        "<?php if (! is_null({$expression->get(0)})) : ?>",
                        "<?php echo {$expression->get(1)}; ?>",
                        '<?php endif; ?>',
                    ]);
                }

                return "<?php if (! is_null({$expression})) : ?>";
            },

            'endisnotnull' => function ($expression) {
                return '<?php endif; ?>';
            },

            /*
            |---------------------------------------------------------------------
            | @mix
            |---------------------------------------------------------------------
            |
            | Usage: @mix('js/app.js') of @mix('css/app.css')
            |
            */

            'mix' => function ($expression) {
                if (Str::endsWith($expression, ".css'")) {
                    return '<link rel="stylesheet" href="<?php echo mix('.$expression.') ?>">';
                }

                if (Str::endsWith($expression, ".js'")) {
                    return '<script src="<?php echo mix('.$expression.') ?>"></script>';
                }

                return "<?php echo mix({$expression}); ?>";
            },

            /*
            |---------------------------------------------------------------------
            | @stylesheet
            |---------------------------------------------------------------------
            |
            | Usage: @addstyle('/css/app.css') or @addstyle body{ color: red; } @endaddstyle
            |
            */

            'addstyle' => function ($expression, bool $linkAttribute = true) {
                if ($linkAttribute) {
                    return '<link rel="stylesheet" href="'.self::stripQuotes($expression).'">';
                }

                return '<style>';
            },

            'endaddstyle' => function (bool $linkAttribute = true) {
                if (!$linkAttribute) {
                    return '</style>';
                }

                return '';
            },

            /*
            |---------------------------------------------------------------------
            | @script
            |---------------------------------------------------------------------
            |
            | Usage: @addscript('/js/app.js') or @addscript alert('Message') @endaddscript
            |
            */

            'addscript' => function ($expression, bool $linkAttribute = true) {
                if ($linkAttribute) {
                    return '<script src="'.self::stripQuotes($expression).'"></script>';
                }

                return '<script>';
            },

            'endscript' => function (bool $linkAttribute = true) {
                if (!$linkAttribute) {
                    return '</style>';
                }
                return '</script>';
            },

            /*
            |---------------------------------------------------------------------
            | @window
            |---------------------------------------------------------------------
            |
            | This directive can be used to add variables to javascript's window
            | Usage: @window('name', ['key' => 'value'])
            |
            */

            'window' => function ($expression) {
                $expression = self::multipleArgs($expression);

                $variable = self::stripQuotes($expression->get(0));

                return  implode("\n", [
                    '<script>',
                    "window.{$variable} = <?php echo is_array({$expression->get(1)}) ? json_encode({$expression->get(1)}) : {$expression->get(1)}; ?>;",
                    '</script>',
                ]);
            },

            /*
            |---------------------------------------------------------------------
            | @inline
            |---------------------------------------------------------------------
            */

            'inline' => function ($expression) {
                $include = implode("\n", [
                    "/* {$expression} */",
                    "<?php include public_path({$expression}) ?>\n",
                ]);

                if (Str::endsWith($expression, ".html'")) {
                    return $include;
                }

                if (Str::endsWith($expression, ".css'")) {
                    return "<style>\n".$include.'</style>';
                }

                if (Str::endsWith($expression, ".js'")) {
                    return "<script>\n".$include.'</script>';
                }
            },

            /*
            |---------------------------------------------------------------------
            | @routeis
            |---------------------------------------------------------------------
            */

            'routeis' => function ($expression) {
                return "<?php if (fnmatch({$expression}, Route::currentRouteName())) : ?>";
            },

            'endrouteis' => function ($expression) {
                return '<?php endif; ?>';
            },

            'routeisnot' => function ($expression) {
                return "<?php if (! fnmatch({$expression}, Route::currentRouteName())) : ?>";
            },

            'endrouteisnot' => function ($expression) {
                return '<?php endif; ?>';
            },

            /*
            |---------------------------------------------------------------------
            | @instanceof
            |---------------------------------------------------------------------
            */

            'instanceof' => function ($expression) {
                $expression = self::multipleArgs($expression);

                return  "<?php if ({$expression->get(0)} instanceof {$expression->get(1)}) : ?>";
            },

            'endinstanceof' => function () {
                return '<?php endif; ?>';
            },

            /*
            |---------------------------------------------------------------------
            | @typeof
            |---------------------------------------------------------------------
            */

            'typeof' => function ($expression) {
                $expression = self::multipleArgs($expression);

                return  "<?php if (gettype({$expression->get(0)}) == {$expression->get(1)}) : ?>";
            },

            'endtypeof' => function () {
                return '<?php endif; ?>';
            },

            /*
            |---------------------------------------------------------------------
            | @dump, @dd
            |---------------------------------------------------------------------
            */

            'dump' => function ($expression) {
                return "<?php dump({$expression}); ?>";
            },

            'dd' => function ($expression) {
                return "<?php dd({$expression}); ?>";
            },

            /*
            |---------------------------------------------------------------------
            | @pushonce
            |---------------------------------------------------------------------
            */

            'pushonce' => function ($expression) {
                [$pushName, $pushSub] = explode(':', trim(substr($expression, 1, -1)));

                $key = '__pushonce_'.str_replace('-', '_', $pushName).'_'.str_replace('-', '_', $pushSub);

                return "<?php if(! isset(\$__env->{$key})): \$__env->{$key} = 1; \$__env->startPush('{$pushName}'); ?>";
            },

            'endpushonce' => function () {
                return '<?php $__env->stopPush(); endif; ?>';
            },

            /*
            |---------------------------------------------------------------------
            | @repeat
            |---------------------------------------------------------------------
            */

            'repeat' => function ($expression) {
                return "<?php for (\$iteration = 0 ; \$iteration < (int) {$expression}; \$iteration++): ?>";
            },

            'endrepeat' => function ($expression) {
                return '<?php endfor; ?>';
            },

            /*
             |---------------------------------------------------------------------
             | @data
             |---------------------------------------------------------------------
             */

            'dataAttributes' => function ($expression) {
                $output = 'collect((array) '.$expression.')
            ->map(function($value, $key) {
                return "data-{$key}=\"{$value}\"";
            })
            ->implode(" ")';

                return "<?php echo $output; ?>";
            },

            /*
            |---------------------------------------------------------------------
            | @fa, @fas, @far, @fal, @fab, @mdi, @glyph
            |---------------------------------------------------------------------
            */

            'fa' => function ($expression) {
                $expression = self::multipleArgs($expression);

                return '<i class="fa fa-'.self::stripQuotes($expression->get(0)).' '.self::stripQuotes($expression->get(1)).'"></i>';
            },

            'fad' => function ($expression) {
                $expression = self::multipleArgs($expression);

                return '<i class="fad fa-'.self::stripQuotes($expression->get(0)).' '.self::stripQuotes($expression->get(1)).'"></i>';
            },

            'fas' => function ($expression) {
                $expression = self::multipleArgs($expression);

                return '<i class="fas fa-'.self::stripQuotes($expression->get(0)).' '.self::stripQuotes($expression->get(1)).'"></i>';
            },

            'far' => function ($expression) {
                $expression = self::multipleArgs($expression);

                return '<i class="far fa-'.self::stripQuotes($expression->get(0)).' '.self::stripQuotes($expression->get(1)).'"></i>';
            },

            'fal' => function ($expression) {
                $expression = self::multipleArgs($expression);

                return '<i class="fal fa-'.self::stripQuotes($expression->get(0)).' '.self::stripQuotes($expression->get(1)).'"></i>';
            },

            'fab' => function ($expression) {
                $expression = self::multipleArgs($expression);

                return '<i class="fab fa-'.self::stripQuotes($expression->get(0)).' '.self::stripQuotes($expression->get(1)).'"></i>';
            },

            'mdi' => function ($expression) {
                $expression = self::multipleArgs($expression);

                return '<i class="mdi mdi-'.self::stripQuotes($expression->get(0)).' '.self::stripQuotes($expression->get(1)).'"></i>';
            },

            'glyph' => function ($expression) {
                $expression = self::multipleArgs($expression);

                return '<i class="glyphicons glyphicons-'.self::stripQuotes($expression->get(0)).' '.self::stripQuotes($expression->get(1)).'"></i>';
            },

            'bi' => function ($expression) {
                $expression = self::multipleArgs($expression);

                return '<i class="bi bi-'.self::stripQuotes($expression->get(0)).' '.self::stripQuotes($expression->get(1)).'"></i>';
            },

            /*
            |---------------------------------------------------------------------
            | @haserror
            |---------------------------------------------------------------------
            */

            'haserror' => function ($expression) {
                return '<?php if (isset($errors) && $errors->has('.$expression.')): ?>';
            },

            'endhaserror' => function () {
                return '<?php endif; ?>';
            },

            /*
            |---------------------------------------------------------------------
            | @count
            |---------------------------------------------------------------------
            |
            | Usage: @count([1,2,3])
            |
            */

            'count' => function ($expression) {
                return '<?php echo '.count(json_decode($expression)).'; ?>';
            },

            /*
            |---------------------------------------------------------------------
            | @nl2br
            |---------------------------------------------------------------------
            */

            'nl2br' => function ($expression) {
                return "<?php echo nl2br($expression); ?>";
            },

            /*
            |---------------------------------------------------------------------
            | @kebab, @snake, @camel
            |---------------------------------------------------------------------
            */

            'kebab' => function ($expression) {
                return '<?php echo '.Str::kebab($expression).'; ?>';
            },

            'snake' => function ($expression) {
                return '<?php echo '.Str::snake($expression).'; ?>';
            },

            'camel' => function ($expression) {
                return '<?php echo '.Str::camel($expression).'; ?>';
            },

            /*
            |---------------------------------------------------------------------
            | @returnifempty
            |---------------------------------------------------------------------
            */

            'returnifempty' => function ($expression) {
                return "<?php if (empty($expression) || ($expression && !count($expression))) { return; } ?>";
            },

            /*
            |---------------------------------------------------------------------
            | @base64Image
            |---------------------------------------------------------------------
            */

            'base64image' => function ($expression) {
                return "<?php echo 'data:image/' . pathinfo($expression, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($expression)); ?>";
            },

            /*
            |---------------------------------------------------------------------
            | @javascript
            |---------------------------------------------------------------------
            */

            'javascript' => function ($expression) {
                $expression = "({$expression})";

                return "<?= app('\Simtabi\Laranail\Nails\Blade\Supports\DirectiveRenderer')->render{$expression}; ?>";
            },

            /*
            |---------------------------------------------------------------------
            | @activeifroute
            |---------------------------------------------------------------------
            */

            'activeifroute' => function ($expression) {
                return "<?php echo strpos(request()->route()->getName(), {$expression}) === 0 ? 'active' : ''; ?>";
            },

            /*
            |---------------------------------------------------------------------
            | @selectedif
            |---------------------------------------------------------------------
            */

            'selectedif' => function ($expression) {
                return "<?php echo $expression ? 'selected' : ''; ?>";
            },

            /*
            |---------------------------------------------------------------------
            | @inputvalue
            |---------------------------------------------------------------------
            */

            'inputvalue' => function ($expression) {
                list($model, $parameter) = explode(',',str_replace(['(',')',' '], '', $expression));
                $parameter = str_replace(["'", '"'], '', $parameter);

                return "<?php if(isset($model)) echo e(old('$parameter', $model->$parameter)); else echo e(old('$parameter')); ?>";
            },

            /*
            |---------------------------------------------------------------------
            | @optionvalue
            |---------------------------------------------------------------------
            */

            'optionvalue' => function ($expression) {
                list($model, $parameter, $default) = explode(',',str_replace(['(',')',' '], '', $expression));
                $parameter = str_replace(["'", '"'], '', $parameter);

                return "<?php if((isset($model) && old('$parameter', $model->$parameter) == $default) || old('$parameter') == $default) echo 'selected=\"selected\"' ?>";
            },

            /*
            |---------------------------------------------------------------------
            | @checkboxvalue
            |---------------------------------------------------------------------
            */

            'checkboxvalue' => function ($expression) {
                list($model, $parameter) = explode(',',str_replace(['(',')',' '], '', $expression));
                $parameter = str_replace(["'", '"'], '', $parameter);

                return "<?php if((isset($model) && old('$parameter', $model->$parameter) == 1) || old('$parameter') == 1) echo 'checked=\"checked\"' ?>";
            },

            /*
            |---------------------------------------------------------------------
            | @checkboxvaluefromarray
            |---------------------------------------------------------------------
            */

            'checkboxvaluefromarray' => function ($expression) {
                list($model, $parameter, $array) = explode(',',str_replace(['(',')',' '], '', $expression));
                $parameter = str_replace(["'", '"'], '', $parameter);

                return "<?php if(collect(old('$parameter', []))->contains(".$model."->id) ||collect($array)->contains(function(\$item) use($model) {
                    return \$item == ".$model."->id;
                })) echo 'checked=\"checked\"' ?>";
            },

        ];

    }

    /**
     * Parse expression.
     *
     * @param  string  $expression
     * @return Collection
     */
    private static function multipleArgs($expression): Collection
    {
        return collect(explode(',', $expression))->map(function ($item) {
            return trim($item);
        });
    }

    /**
     * Strip quotes.
     *
     * @param  string  $expression
     * @return string
     */
    private static function stripQuotes($expression): string
    {
        return str_replace(["'", '"'], '', $expression);
    }

}