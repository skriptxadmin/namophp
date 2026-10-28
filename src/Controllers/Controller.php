<?php
namespace App\Controllers;

use App\Helpers\CSRF;
use function _\get;
use Slim\Routing\RouteContext;
use Smarty\Smarty;
use App\Helpers\DB;

class Controller extends DB
{


    public $cache_path = ABSPATH . '/./cached';

    public $cache_time;

    public $smarty;

    public function __construct()
    {
        parent::__construct();
        
        $this->cache_time = $_ENV['CACHE_TIME'];

    }

    public function cache_key(string $prefix, array $params = []): string
    {
        ksort($params);

        return $prefix . '-' . md5(json_encode($params));
    }

/**
 * Cache any file.
 *
 * For .json files, $data is encoded as JSON.
 * For other files, $data is written as-is.
 */
    public function set_cached_file($file, $data)
    {
        $ENV = get($_ENV, 'APP_ENV', 'production');

        if ($ENV !== 'production') {
            return;
        }

        $cacheDir = $this->cache_path;

        if (! is_dir($cacheDir) && ! mkdir($cacheDir, 0755, true) && ! is_dir($cacheDir)) {
            return false;
        }

        $file      = ltrim(trim($file), '/\\');
        $cacheFile = $cacheDir . DIRECTORY_SEPARATOR . $file;

        // Create nested directories if required.
        $directory = dirname($cacheFile);

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            return false;
        }

        // Encode only JSON files.
        if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'json') {
            $data = json_encode($data);

            if ($data === false) {
                return false;
            }
        }

        return file_put_contents($cacheFile, $data, LOCK_EX) !== false;
    }

/**
 * Retrieve any cached file.
 *
 * For .json files, return decoded data.
 * For other files, return raw contents.
 *
 * $time is the cache lifetime in seconds.
 */
    public function get_cached_file($file, $time = null)
    {
        $file      = ltrim(trim($file), '/\\');
        $cacheFile = $this->cache_path . DIRECTORY_SEPARATOR . $file;

        $cacheTime = ($time !== null && $time !== '')
            ? (int) $time
            : $this->cache_time;

        if (
            ! is_file($cacheFile) ||
            (time() - filemtime($cacheFile)) >= $cacheTime
        ) {
            return null;
        }

        $data = file_get_contents($cacheFile);

        if ($data === false) {
            return null;
        }

        if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'json') {
            return json_decode($data, true);
        }

        return $data;
    }
    public function redirect($request, $urlname, array $data = [])
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url         = $routeParser->urlFor($urlname, $data);

        $response = new \Slim\Psr7\Response();

        return $response
            ->withHeader('Location', $url)
            ->withStatus(302);
    }

    public function json($data = [], $status = 200)
    {

        $payload = json_encode($data);

        $response = new \Slim\Psr7\Response();

        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);

    }

    public function xml($request, $tpl, $data = [])
    {
        $smarty = new Smarty();

               $smarty->setTemplateDir(ABSPATH . '/src/Views/');


        foreach ($data as $key => $value) {
            $smarty->assign($key, $value);
        }

        $xml = $smarty->fetch($tpl . '.xml');

        $response = new \Slim\Psr7\Response();

        $response->getBody()->write($xml);

        return $response->withHeader(
            'Content-Type',
            'application/xml; charset=UTF-8'
        );
    }
    public function html($request, $tpl, $data = null, $page_cache = null)
    {

        $smarty = new Smarty();
        $smarty->setTemplateDir(ABSPATH . '/src/Views/');
        $smarty->setCompileDir(ABSPATH . '/smarty/templates_c/');
        $smarty->setConfigDir(ABSPATH . '/smarty/configs/');
        $smarty->setCacheDir(ABSPATH . '/smarty/cache/');

        $ENV                   = get($_ENV, 'APP_ENV', 'production');
        $smarty->caching       = Smarty::CACHING_OFF;
        $smarty->compile_check = true;
        $smarty->force_compile = true;

        if ($ENV == 'production' && $page_cache !== false) {

            $smarty->caching        = Smarty::CACHING_LIFETIME_CURRENT;
            $smarty->compile_check  = false;
            $smarty->force_compile  = false;
            $smarty->cache_lifetime = $this->cache_time;
        }

        $response = new \Slim\Psr7\Response();

        $routeContext = RouteContext::fromRequest($request);
        $route        = $routeContext->getRoute();
        $routeName    = $route->getName();
        $smarty->assign('pageClass', '');
        if ($routeName) {

            $explode = explode('.', $routeName);
            $implode = implode(' ', $explode);
            $smarty->assign('pageClass', $implode);
        }

        $meta = [
            'title'       => get($_ENV, 'APP_TITLE', ''),
            'description' => get($_ENV, 'APP_DESCRIPTION', ''),
            'image'       => get($_ENV, 'APP_META_IMAGE', ''),
            'url'         => $request->getUri(),
        ];
        if (! empty($data)) {
            foreach ($data as $key => $value) {
                $smarty->assign($key, $value);
            }

            if (! empty($data['meta'])) {
                $meta = array_merge($meta, $data['meta']);
            }
        }

        $smarty->assign('meta', $meta);

        $csrfContext = $_ENV['AJAX_CSRF_CONTEXT'];
        $csrf        = new CSRF($csrfContext);
        $csrf->clearHashes($csrfContext);
        $hash = $csrf->string($csrfContext);
        $smarty->assign('csrfToken', $hash);

        $smarty->assign('isUserLoggedIn', ! empty($_SESSION['user_username']) ? 1 : 0);

        $ENV = get($_ENV, 'APP_ENV', 'production');
        $smarty->assign('isProduction', $ENV == 'production' ? 1 : 0);
        $smarty->assign('url', $_ENV['APP_URL']);

        $smarty->registerFilter("output", [$this, 'minify_html']);
        $smarty->registerPlugin("function", 'env', [$this, 'env']);
        $smarty->registerPlugin("function", 'svg', [$this, 'svg']);
        $smarty->registerPlugin("function", 'js', [$this, 'js']);
        $smarty->registerPlugin("function", 'css', [$this, 'css']);

        $html = $smarty->fetch($tpl . '.html', $page_cache);

        $response->getBody()->write($html);

        return $response;

    }

    public function minify_html($tpl_output, $template)
    {
        $ENV = get($_ENV, 'APP_ENV', 'production');

        if ($ENV === 'production') {
            // Remove whitespace between HTML tags only
            $tpl_output = preg_replace('/>\s+</', '><', $tpl_output);

            // Remove spaces and tabs at line boundaries, not within attributes
            $tpl_output = preg_replace('/^[\t ]+|[\t ]+$/m', '', $tpl_output);

            // Remove empty lines
            $tpl_output = preg_replace('/\n{2,}/', "\n", $tpl_output);
        }

        return $tpl_output;
    }

    public function env($params = [])
    {

        if (empty($params['var'])) {

            return '';
        }

        return get($_ENV, get($params, 'var', 'DEFAULT'), '');
    }
    public function svg($params, $template)
    {
        if (! empty($params['file'])) {
            $path = ABSPATH . '/public/svgs/' . $params['file'] . '.svg';
            return file_exists($path) ? file_get_contents($path) : '';
        }
        return '';
    }

    public function js($params, $template)
    {
        $file = isset($params['file']) ? $params['file'] : '';
        if (! $file) {
            return '';
        }

        // Version number (can be static or dynamic)
        $version = isset($_ENV['APP_VERSION']) ? $_ENV['APP_VERSION'] : '1.0.0';

        // Optional: base URL
        $baseUrl = isset($_ENV['url']) ? rtrim($_ENV['url'], '/') : '';

        $src = ($baseUrl ? $baseUrl . '/' : '') . $file . '?v=' . $version;

        return '<script src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '"></script>';
    }

    public function css($params, $template)
    {
        $file = isset($params['file']) ? $params['file'] : '';
        if (! $file) {
            return '';
        }

        // Version number (can be static or dynamic)
        $version = isset($_ENV['APP_VERSION']) ? $_ENV['APP_VERSION'] : '1.0.0';

        // Optional: base URL
        $baseUrl = isset($_ENV['APP_URL']) ? rtrim($_ENV['APP_URL'], '/') : '';

        $src = ($baseUrl ? $baseUrl . '/' : '') . $file . '?v=' . $version;

        return '<link href="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" rel="stylesheet" />';
    }

}
