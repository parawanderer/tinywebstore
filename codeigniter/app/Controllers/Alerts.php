<?php

namespace App\Controllers;

use App\Models\AlertModel;
use CodeIgniter\RESTful\ResourceController;

class Alerts extends ResourceController
{
    // not really the best approach to this but oh well
    public function index()
    {
        if (!$this->loggedIn()) {
            $this->response->setStatusCode(404);
            return;
        }

        /** @var \App\Models\AlertModel */
        $alertModel = model(AlertModel::class);

        $unread = $alertModel->getUnseenAlerts($this->getCurrentUserId(), 10, false);
        Alerts::convertTime($unread);
        
        // we'll be managing the DOM elements using JS
        return $this->response->setJSON($unread);
    }

    private static function convertTime(array &$alerts) {
        if (empty($alerts)) return;

        foreach($alerts as &$alert) {
            $alert['timestamp'] = strtotime($alert['timestamp']);
        }
    }

    public function seen() {
        if (!$this->loggedIn()) {
            $this->response->setStatusCode(403);
            return;
        }

        $json = $this->request->getJSON(true);

        if (empty($json) || !$json['id'])
            return $this->response->setStatusCode(400, 'Bad Request');

        $alertId = $json['id'];

        /** @var \App\Models\AlertModel */
        $alertModel = model(AlertModel::class);
        $result = $alertModel->markRead($alertId, $this->getCurrentUserId());

        if ($result) {
            $this->response->setStatusCode(202);
            return;
        }
        $this->response->setStatusCode(500, "Internal Server Error");
        return;
    }


    private function loggedIn() {
        $session = \Config\Services::session();
        return !!$session->get('username');
    }

    private function getCurrentUserId() {
        $session = \Config\Services::session();
        return $session->get('user_id');
    }
}