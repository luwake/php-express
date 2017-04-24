<?php
namespace Luwake;

interface Handler
{
    public function __invoke(Request $request, Request $response, $next);
}
