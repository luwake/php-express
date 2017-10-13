<?php
namespace Luwake;

use Opis\Http\Request as HttpRequest;
use Luwake\Traits\ObjectTrait;
use Luwake\Traits\ExtendTrait;
use Aura\Accept\AcceptFactory;
use Opis\Http\ProxyHandler;

class Request extends HttpRequest implements \ArrayAccess
{
    use ObjectTrait,ExtendTrait;

    public $app;

    public $accept;

    public function __construct(Express $app)
    {
        parent::__construct($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER, null);
        $this->app = $app;
        $this->proxy = new ProxyHandler();
    }

    private function accept()
    {
        if (! $this->accept) {
            $this->accept = (new AcceptFactory($_SERVER))->newInstance();
        }
        return $this->accept;
    }

    public function is($types)
    {
        return $this->accepts($types);
    }

    public function accepts($types)
    {
        return $this->accept()
            ->negotiateMedia($types)
            ->getValue();
    }

    public function acceptsCharsets($charset)
    {
        return $this->accept()
            ->negotiateCharset($charset)
            ->getValue();
    }

    public function acceptsEncodings($encoding)
    {
        return $this->accept()
            ->negotiateEncoding($encoding)
            ->getValue();
    }

    public function acceptsLanguages($lang)
    {
        return $this->accept()
            ->negotiateLanguage($lang)
            ->getValue();
    }

    public function param($name, $defaultValue = null)
    {
        if (($value = $this->get($name)) !== null) {
            return $value;
        }
        if (($value = $this->post($name)) !== null) {
            return $value;
        }
        if (($value = $this->file($name)) !== null) {
            return $value;
        }
        if (($value = $this->getData($name)) !== null) {
            return $value;
        }
        return $defaultValue;
    }
}