<?php

require_once 'modules/admin/models/ServerPlugin.php';

class PluginSample extends ServerPlugin {

    public $features = [
        'packageName' => true,
        'testConnection' => true,
        'showNameservers' => false,
        'directlink' => true
    ];

    public function getVariables()
    {
        $variables = [
            'Name' => [
                'type' => 'hidden',
                'description' => 'Used by CE to show plugin',
                'value' => 'Sample'
            ],
            'Description' => [
                'type' => 'hidden',
                'description' => 'Description viewable by admin in server settings',
                'value' => 'Sample Server Plugin'
            ],
            'Text Field' => [
                'type' => 'text',
                'description' => 'Text Field Description',
                'value' => 'Default Value',
            ],
            'Encrypted Text Field' => [
                'type' => 'text',
                'description' => 'Encrypted Text Field Description',
                'value' => '',
                'encryptable' => true
            ],
            'Password Text Field' => [
                'type' => 'password',
                'description' => 'Encrypted Password Field Description',
                'value' => '',
                'encryptable' => true
            ],
            'Text Area' => [
                'type' => 'textarea',
                'description' => 'Text Area Description',
                'value' => 'Default Value',
            ],
            'Yes / No' => [
                'type' => 'yesno',
                'description' => 'Yes / No Description',
                'value' => '1',
            ],
            'Actions' => [
                'type' => 'hidden',
                'description' => 'Current actions that are active for this plugin per server',
                'value'=>'Create,Delete,Suspend,UnSuspend'
            ],
            'Registered Actions For Customer' => [
                'type' => 'hidden',
                'description' => 'Current actions that are active for this plugin per server for customers',
                'value' => 'authenticateClient'
            ],
            'package_addons' => [
                'type' => 'hidden',
                'description' => 'Supported signup addons variables',
                'value' => ['DISKSPACE', 'BANDWIDTH', 'SSL']
            ],
            'package_vars' => [
                'type' => 'hidden',
                'description' => 'Whether package settings are set',
                'value' => '1',
            ],
            'package_vars_values' => [
                'type'  => 'hidden',
                'description' => lang('Package Settings'),
                'value' => [
                    'Text Field' => [
                        'type' => 'text',
                        'label' => 'Text Field Label',
                        'description' => 'Text Field Description',
                        'value' => 'Default Value',
                    ],
                    'Drop Down' => [
                        'type' => 'dropdown',
                        'multiple' => false,
                        'getValues' => 'getDropDownValues',
                        'label' => 'Drop Down Label',
                        'description' => 'Drop Down Description',
                        'vaue' => '',
                    ]
                ]
            ]
        ];

        return $variables;
    }

    public function validateCredentials($args)
    {
    }

    public function doDelete($args)
    {
        $userPackage = new UserPackage($args['userPackageId']);
        $args = $this->buildParams($userPackage);
        $this->delete($args);
        return 'Package has been deleted.';
    }

    public function doCreate($args)
    {
        $userPackage = new UserPackage($args['userPackageId']);
        $args = $this->buildParams($userPackage);
        $this->create($args);
        return 'Package has been created.';
    }

    public function doSuspend($args)
    {
        $userPackage = new UserPackage($args['userPackageId']);
        $args = $this->buildParams($userPackage);
        $this->suspend($args);
        return 'Package has been suspended.';
    }

    public function doUnSuspend($args)
    {
        $userPackage = new UserPackage($args['userPackageId']);
        $args = $this->buildParams($userPackage);
        $this->unsuspend($args);
        return 'Package has been unsuspended.';
    }

    public function unsuspend($args)
    {
        // Call Unsuspend at the server
    }

    public function suspend($args)
    {
        // Call suspend at the server
    }

    public function delete($args)
    {
        // Call delete at the server
    }

    public function getAvailableActions($userPackage)
    {
        $args = $this->buildParams($userPackage);

        $actions = [];
        // Get Status at Server

        // If not created yet
        $actions[] = 'Create';

        // If we can delete
        $actions[] = 'Delete';

        // If we can suspend
        $actions[] = 'Suspend';

        // If suspended at Server
        $actions[] = 'UnSuspend';
        return $actions;
    }

    public function create($args)
    {
        $userPackage = new UserPackage($args['package']['id']);

        // call create at the server
        // If we need to store custom data for later
        $userPackage->setCustomField('Server Acct Properties', 'Virtual Server Id');
    }

    public function testConnection($args)
    {
        CE_Lib::log(4, 'Testing connection to SolusVM server');
        $this->setup($args);

        $params = array();
        $params['action'] = 'node-idlist';
        // we send openvz, just as a test, to see if we can connect or not.
        $params['type'] = 'openvz';
        $response = $this->call($params, $args);
    }

    public function getDropDownValues()
    {
        $values = [
            '0' => 'Zero',
            '1' => 'One',
            '2' => 'Two'
        ];

        return $values;
    }

    public function getDirectLink($userPackage, $getRealLink = true)
    {
        $linkText = $this->user->lang('Login to Server');
        $args = $this->buildParams($userPackage);

        if ($getRealLink) {
            // call login at server

            return [
                'link'    => '<li><a target="_blank" href="url to login">' .$linkText . '</a></li>',
                'rawlink' =>  'url to login',
                'form'    => ''
            ];
        } else {
            return [
                'link' => '<li><a target="_blank" href="index.php?fuse=clients&controller=products&action=openpackagedirectlink&packageId='.$userPackage->getId().'&sessionHash='.CE_Lib::getSessionHash().'">' .$linkText . '</a></li>',
                'form' => ''
            ];
        }
    }
}