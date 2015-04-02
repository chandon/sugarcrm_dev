<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

require_once('tests/rest/RestTestBase.php');

class RestPublicMetadataViewTemplatesTest extends RestTestBase {

    /**
     * @group rest
     */
    public function testMetadataViewTemplates() {
        $restReply = $this->_restCall('metadata/public?type_filter=views');

        $this->assertTrue(isset($restReply['reply']['views']['_hash']),'Views hash is missing.');
    }

    /**
     * @group rest
     */
    public function testMetadataViewTemplatesHbs() {
        $filesToCheck = array(
            'base' => array(
                'clients/base/views/edit/edit.hbs',
                'custom/clients/base/views/edit/edit.hbs',
            ),
        );
        SugarTestHelper::saveFile($filesToCheck);

        $dirsToMake = array(
            'base' => array(
                'clients/base/views/edit',
                'custom/clients/base/views/edit',
            ),
        );

        foreach ($dirsToMake as $platformDirs) {
            foreach ($platformDirs as $dir) {
                SugarAutoLoader::ensureDir($dir);
            }
        }


        // Make sure we get the base code when we ask for it.
        SugarAutoLoader::put($filesToCheck['base'][0],'BASE CODE', true);
        $this->_clearMetadataCache();
        $restReply = $this->_restCall('metadata/public?type_filter=views&platform=base');
        $this->assertEquals('BASE CODE',$restReply['reply']['views']['edit']['templates']['edit'],"Didn't get base code when it was the direct option");


        // Make sure custom base code works
        SugarAutoLoader::put($filesToCheck['base'][1],'CUSTOM BASE CODE', true);
        $this->_clearMetadataCache();
        $restReply = $this->_restCall('metadata/public?type_filter=views');
        $this->assertEquals('CUSTOM BASE CODE',$restReply['reply']['views']['edit']['templates']['edit'],"Didn't use the custom base code.");

    }
}
