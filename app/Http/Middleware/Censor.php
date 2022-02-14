<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\BlackList;
use Closure;
use Illuminate\Http\Request;

class Censor
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private $blackList;

    public function __construct() {
        $this->blackList = $this->getBlackList();
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $params = $request->all();

        if (isset($params['description'])) {
            $params['description'] = $this->convert($params['description']);
            $request->replace($params);
        }

        if (isset($params['title'])) {
            $params['title'] = $this->convert($params['title']);
            $request->replace($params);
        }

        return $next($request);
    }

    /**
     * @param $input
     * @return string
     */
    protected function convert($input): string {
        $input = mb_strtolower($input);
        $pattern = '@(http(s)?://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
        $input = preg_replace($pattern, '***', $input);

        return str_replace($this->blackList, '***', $input);
    }

    protected function getBlackList()
    {
        return BlackList::all()->pluck('word')->toArray();
    }
}
