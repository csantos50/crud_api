<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Category\Controller;

use Category\Model\CategoryTable;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{

    protected $table;

    public function __construct()
    {
        $this->table = new CategoryTable(null);
    }

    public function indexAction()
    {

        $obj = $this->table->fetchAll();
        $this->send('ok', $obj);
    }

    public function allAction()
    {

        $obj = $this->table->fetchAll();
        $this->send('ok', $obj);
    }

    public function createAction()
    {

        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $post = file_get_contents('php://input');
        $data = json_decode($post);

        if ($data->name == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $date = new \DateTime();
        $now = $date->format("Y-m-d H:i:s");

        $data = [
            "name" => $data->name,
            "created" => $now,
            "modified" => $now
        ];
        $response = $this->table->save($data);
        $this->send('Category created successful', $response);
    }

    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id', -1);

        if ($id < 0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $category = $this->table->findBy(['id' => $id]);

        if ($category == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $this->send('ok', $category);
    }

    public function searchAction()
    {
        $post = file_get_contents('php://input');
        $data = json_decode($post);

        if (count($data) > 0) {

            if ($data->id) {
                $find['id'] = $data->id;
            }
            if ($data->name) {
                $find['name'] = $data->name;
            }
            if ($data->created) {
                $find['created'] = $data->created;
            }
            if ($data->modified) {
                $find['modified'] = $data->modified;
            }
            $category = $this->table->findBy($find);
            if ($category == null) {
                $this->getResponse()->setStatusCode(404);
                return;
            } else {
                $this->send('ok', $category);
            }
        }


        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function editAction()
    {
        if ($this->getRequest()->isPost()) {

        }
        $id = (int) $this->params()->fromRoute('id', -1);

        if ($id < 0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $category = reset($this->table->findBy(['id' => $id]));

        if ($category == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $post = file_get_contents('php://input');
        $data = json_decode($post);

        if ($data->name == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $date = new \DateTime();
        $now = $date->format("Y-m-d H:i:s");

        $category['name'] = $data->name;
        $category['modified'] = $now;

        $response = $this->table->update($category);
        $this->send('Category edit successful', $response);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', -1);

        if ($id < 0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $category = reset($this->table->findBy(['id' => $id]));

        if ($category == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $response = $this->table->delete($id);

        $this->send('Category delete successful', []);
    }

    // Send Json Response

    public function send($msg, $obj)
    {
        $returnArray["status"] = "success";
        $returnArray["message"] = $msg;
        $returnArray["data"] = $obj;
        $response = new Response();
        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->setContent($obj);
        self::sendResponse(200, json_encode($returnArray), "application/json");
    }

    private function sendResponse($status = 200, $body = '', $content_type = 'application/json')
    {
        $status_header = 'HTTP/1.1 '.$status.' '.$this->getStatusCodeMessage($status);
        // set the status
        header($status_header);
        // set the content type
        header('Content-type: '.$content_type);
        //header('Last-modified: ' . $last_modified);
        // pages with body are easy
        if ($body != '') {
            // send the body
            echo $body;
            exit;
        }
        // we need to create the body if none is passed
        else {
            // create some body messages
            $message = '';

            // this is purely optional, but makes the pages a little nicer to read
            // for your users.  Since you won't likely send a lot of different status codes,
            // this also shouldn't be too ponderous to maintain
            switch ($status) {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL '.$_SERVER['REQUEST_URI'].' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }

            // servers don't always have a signature turned on (this is an apache directive "ServerSignature On")
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'].' Server at '.$_SERVER['SERVER_NAME'].' Port '.$_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

            // this should be templatized in a real-world solution
            $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
                        <html>
                            <head>
                                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                                <title>'.$status.' '.$this->_getStatusCodeMessage($status).'</title>
                            </head>
                            <body>
                                <h1>'.$this->_getStatusCodeMessage($status).'</h1>
                                <p>'.$message.'</p>
                                <hr />
                                <address>'.$signature.'</address>
                            </body>
                        </html>';

            echo $body;
            exit;
        }
    }

    /**
     * Gets the message for a status code
     *
     * @param mixed $status status code
     */
    private function getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );

        return (isset($codes[$status])) ? $codes[$status] : '';
    }

}
