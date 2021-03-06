<?php 
/**
 * Openbiz Cubi 
 *
 * LICENSE
 *
 * This source file is subject to the BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @package   user.form
 * @copyright Copyright &copy; 2005-2009, Rocky Swen
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @link      http://www.phpopenbiz.org/
 * @version   $Id$
 */

include_once MODULE_PATH."/system/form/UserForm.php";

/**
 * AccountEditForm class - implement the logic of edit my account form
 *
 * @package user.form
 * @author Rocky Swen
 * @copyright Copyright (c) 2005-2009
 * @access public
 */
class AccountEditForm extends UserForm
{
    protected $_userId = null;
    
    function __construct(&$xmlArr)
    {
        parent::__construct($xmlArr);
        
        // read user profile and set fix search rule
        global $g_BizSystem;
        $profile = $g_BizSystem->getUserProfile();
        if ($profile && $profile['Id'])
            $this->_userId = $profile['Id'];
    }
    
    public function render()
    {
        // set fix search rule
        if (!$this->_userId)
            return BizSystem::clientProxy()->redirectView(ACCESS_DENIED_VIEW);
        $this->m_FixSearchRule = "[Id]=".$this->_userId;
        return parent::render();
    }
    
    public function rerender()
    {
        // clean active record to force query again
        $this->m_ActiveRecord = null;
        // set fix search rule
        if (!$this->_userId)
            return BizSystem::clientProxy()->redirectView(ACCESS_DENIED_VIEW);
        $this->m_FixSearchRule = "[Id]=".$this->_userId;
        return parent::rerender();
    }
    
    /**
     * Update account with user inputs
     *
     * @return void
     */
    public function UpdateAccount()
    {
        $currentRec = $this->fetchData();
        $recArr = $this->readInputRecord();
        $this->setActiveRecord($recArr);
        
        try
        {
            $this->ValidateForm();
        }
        catch (ValidationException $e)
        {
            $this->processFormObjError($e->m_Errors);
            return;
        }

        if (count($recArr) == 0)
            return;

        $password = BizSystem::ClientProxy()->GetFormInputs("fld_password");            
        if($password){
        	$recArr['password'] = hash(HASH_ALG, $password);
		}
        if ($this->_doUpdate($recArr, $currentRec) == false)
            return;
        $this->processPostAction();
/***		
        $this->_doUpdate($recArr, $currentRec);
        
        // if 'notify email' option is checked, send confirmation email to user email address
        // ...
        
        $this->m_Notices[] = $this->GetMessage("USER_DATA_UPDATED");

       	//run eventlog        
        $eventlog 	= BizSystem::getService(EVENTLOG_SERIVCE);        
    	$eventlog->log("USER_MANAGEMENT", "MSG_USER_RESET_PASSWORD");        
        
        $this->rerender();
***/
    }
   
}  
?>
