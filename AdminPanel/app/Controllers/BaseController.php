<?php
/**
 * By MahmoudAp
 * Github: https://github.com/mahmoud-ap
 */

namespace App\Controllers;

class BaseController
{
    public $cont;
    public $templateName = "layouts/admin-layout.php";

    protected $data = array(
        'pageTitle'         => '',
        'viewContent'       => '',
        'activeMenu'        => 'dashboard',
        'activeTheme'       => 'light',
        'activePage'        => 'dashboard',
        'appVersion'        => 1,
        'showUpNotice'      => false,
    );

    function __construct($cont)
    {
        $this->cont = $cont;

        if (!empty($_COOKIE["panel-theme"])) {
            $this->data["activeTheme"] = $_COOKIE["panel-theme"];
        }

        $appVersion =  \App\Models\Settings::getSetting("app_version");
        if ($appVersion) {
            $this->data["appVersion"] = number_format((float)$appVersion, 1, '.', '');
            $lastVersion =  \App\Models\Settings::getSetting("app_last_version");
            if ($lastVersion && $lastVersion > $appVersion) {
                $this->data["showUpNotice"] = true;
            }
        }
    }


    public function __get($var)
    {
        return container()->{$var};
    }

    protected function initRoute($req, $res)
    {
        $this->request = $req;
        $this->response = $res;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function render($data)
    {
        $prefixPageTitle = "Cyber-Panel";
        $this->data = array_merge($this->data, $data);
        $pageTitle  =  $this->data["pageTitle"] . " | " . $prefixPageTitle;
        $this->data["pageTitle"] = $pageTitle;

        $userInfo = getSessionUser();
        if ($userInfo) {
            $this->data["userInfo"] = $userInfo;
            $this->data["userRole"] = $userInfo["role"];
        }


        return $this->cont->view->render($this->response, $this->templateName, $this->data);
    }


    public function renderAjxView($data)
    {
        $html = $this->fetch($data);
        return $this->response->withStatus(200)->withJson(["html" => $html]);
    }

    public function fetch($data)
    {
        $this->templateName = 'layouts/ajax-layout.php';
        $this->data         = array_merge($this->data, $data);

        $userInfo = getSessionUser();
        if ($userInfo) {
            $this->data["userInfo"] = $userInfo;
            $this->data["userRole"] = $userInfo["role"];
        }

        return $this->cont->view->fetch($this->templateName, $this->data);
    }
}
