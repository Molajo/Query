<?php
namespace Molajo\Controller;


/**
 * The delete controller uses model registry data and HTTP post variables to verifying foreign key restraints,
 * and permissions, etc, archive version history, and delete data. The delete controller also schedules the
 * before and after delete event.
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class HOLDupdate extends Controller
{
    /**
     * cancelItem
     *
     * Method to cancel an edit.
     *
     * Tasks: cancel and close
     *
     * @return Boolean
     * @since    1.0
     */
    public function cancel()
    {
        return $this->cancelItem();
    }

    public function close()
    {
        return $this->cancelItem();
    }

    public function cancelItem()
    {
        /** security token **/
//        JRequest::checkToken() or die;

        /** Check In Item **/
        if ($this->get('id') == 0) {
        } else {
            $results = parent::checkinItem();
        }
        /** success message **/
        // successful redirect

    }

    /**
     * restore
     *
     * Method to restore a version history record to the current version.
     *
     * uses saveItem to process save after preparing the data
     *
     * @return Boolean
     * @since    1.0
     */
    public function restore()
    {
        if ($this->get('version_management', 1) == 1) {
        } else {
            $this->redirectClass->setRedirectMessage(
                $this->language->translate('RESTORE_DISABLED_IN_CONFIGURATION')
            );
            $this->redirectClass->setRedirectMessageType($this->language->translate('error'));

            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** Model: Get Data for Restore ID **/
        $data = $this->model->restore($this->get('id'));

        /** Version_History: reset ids to point to current row **/
        JRequest::setVar('from_id', $this->get('id'));
        JRequest::setVar('id', $data->id);
        $this->set('id', $data->id);
        $this->model->reset();

        return $this->saveItem($data, 'save');
    }

    /**
     * saveItemBatch
     *
     * Called from UpdateController::processItem to obtain a current row and prepare data for a new item
     *
     * uses saveItem to process save after preparing the data
     *
     * @return Boolean
     * @since    1.0
     */
    public function saveItemBatch($action)
    {
        /** initialisation */
        $results = parent::initialise('save');

        /** Model: Get Data for Copy ID **/
        if ($action == 'copy') {
            $data = $this->model->copy($this->get('id'), $this->batch_category_id);

            /** reset ids to point to current row **/
            JRequest::setVar('from_id', $this->get('id'));
            JRequest::setVar('id', 0);

            $this->set('id', 0);
            $this->model->reset();
        } else {
            $data = $this->model->move($this->get('id'), $this->batch_category_id);
        }

        return $this->saveItem($data, 'save');
    }

    /**
     * apply, create, save, saveascopy, saveandnew
     *
     * Methods used to save a record with different redirect results.
     *
     * Tasks: apply, create, save, saveascopy, saveandnew all processed by saveItemForm to prepare data
     * and then SaveItem to actually save the data
     *
     * @return Boolean
     * @since    1.0
     */
    public function apply()
    {
        return $this->saveItemForm('apply');
    }

    public function create()
    {
        return $this->saveItemForm('create');
    }

    public function save()
    {
        return $this->saveItemForm('save');
    }

    public function saveascopy()
    {
        return $this->saveItemForm('saveascopy');
    }

    public function saveandnew()
    {
        return $this->saveItemForm('saveandnew');
    }

    /**
     * saveItemForm
     *
     * Used to obtain form data and send to saveItem for save processing
     *
     * Method called by apply, create, save, saveascopy and saveandnew actions
     *
     * @return Boolean
     * @since    1.0
     */
    public function saveItemForm($action)
    {
        /** security token **/
        JRequest::checkToken() or die;

        $results = parent::initialise('save');

        $data = JRequest::getVar('jform', array(), 'post', 'array');

        /** Preparation: save as copy id and action cleanup **/
        if ($action == 'saveascopy') {
            $this->set('id', 0);
            $data['id'] = 0;
            $action     = 'apply';
            JRequest::setVar('id', 0);
        }

        return $this->saveItem($data, $action);
    }

    /**
     * saveItem
     *
     * Method to save a record from a form, as a copy of another record, or using a version history restore.
     *
     * Calling methods include: saveItemForm, saveItemBatch,
     *
     * Also batch-copy uses SaveItem, as well
     *
     * @return Boolean
     * @since    1.0
     */
    public function saveItem($data, $action = null)
    {
        /** security token **/
        JRequest::checkToken() or die;

        /** action **/
        if ($action == null) {
            $action = $this->getTask();
        }

        /** Edit: Must have data from form input, copy or restore action **/
        if (empty($data)) {
            $this->redirectClass->setRedirectMessage(
                $this->language->translate('SAVE_ITEM_TASK_HAS_NO_DATA_TO_SAVE')
            );
            $this->redirectClass->setRedirectMessageType($this->language->translate('warning'));

            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** Edit: check for valid status **/
        if ($this->model->status == STATUS_ARCHIVED) {
            $this->redirectClass->setRedirectMessage($this->language->translate('ARCHIVED_ROW_CANNOT_BE_CHANGED'));
            $this->redirectClass->setRedirectMessageType($this->language->translate('error'));

            return $this->redirectClass->setSuccessIndicator(false);
        }
        if ($this->model->status == STATUS_TRASHED) {
            $this->redirectClass->setRedirectMessage($this->language->translate('TRASHED_ROW_CANNOT_BE_CHANGED'));
            $this->redirectClass->setRedirectMessageType($this->language->translate('error'));

            return $this->redirectClass->setSuccessIndicator(false);
        }
        if ($this->model->status == STATUS_VERSION) {
            $this->redirectClass->setRedirectMessage(
                $this->language->translate('MolajoVersion_ROW_CANNOT_BE_CHANGED')
            );
            $this->redirectClass->setRedirectMessageType($this->language->translate('error'));

            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** Preparation: Save form or version data **/
        $this->user->setUserState(JRequest::getInt('datakey'), $data);
        $context = $this->row['option'] . '.' . JRequest::getCmd('view') . '.' . JRequest::getCmd(
                'view'
            ) . '.' . $action . '.' . JRequest::getInt('datakey');

        /** Edit: verify checkout **/
        if ((int)$this->get('id')) {
            $results = $this->verifyCheckout($this->get('id'));
            if ($results === false) {
                return $this->redirectClass->setSuccessIndicator(false);
            }
        }

        /** Model: Get Form **/
        /** Model Plugin_Event: contentPrepareData **/
        /** Model Plugin_Event: contentPrepareForm **/
        /** Molajo_Note: Forms are named with the concatenated values of option, EditView, view, action, id, datakey separated by '.' **/
        $form = $this->model->getForm($data, false);
        if ($form === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** Model: Validate and Filter **/
        $validData = $this->model->validate($form, $data);

        if ($validData === false) {

            $errors = $this->model->getErrors();

            for ($e = 0; $e < count($errors); $e ++) {
                if (MolajoError::isError($errors[$e])) {
                    $this->message->set($errors[$e]->getMessage(), 'warning');
                } else {
                    $this->message->set($errors[$e], 'warning');
                }
            }

            // $this->registry->get('UserState', 'id')
            //     ->setUserState(JRequest::getInt('datakey'), $data);
            return $this->redirectClass->setSuccessIndicator(false);
        }

        $this->user
            ->setUserState(JRequest::getInt('datakey'), $validData);

        /** Plugin_Event: onContentValidateForm **/
        /** Molajo_Note: onContentValidateForm is a new event that follows the primary source validation **/
        $results = $this->dispatcher->plugin('onContentValidateForm', array($form, $validData));
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** ACL **/
        $results = $this->checkTaskPermissions($checkTask = $action);
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /**
         * Pre-Save Database Processing
         */

        /** Check In Item **/
        $results = parent::checkinItem();
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** Version_history: create **/
        $results = $this->createVersion($context);
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** Plugin_Event: onContentBeforeSave **/
        $results = $this->dispatcher->plugin('onContentBeforeSave', array($context, $validData, $this->isNew));
        if (in_array(false, $results, true)) {
            $this->setError($this->dispatcher->getError());

            return $this->redirectClass->setSuccessIndicator(false);
        }

        /**             **/
        /** Model: Save **/
        /**             **/
        $results = $this->model->save($validData);
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        $this->get('id', (int)$results);
        $validData->id = $this->get('id');

        /** Event: onContentSaveForm **/
        /** Molajo_Note: New Event onContentSaveForm follows primary content save to keep data insync **/
        $results = $this->dispatcher->plugin('onContentSaveForm', array($form, $validData));
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** clear session data **/
        $this->user->setUserState(JRequest::getInt('datakey'), null);

        /** Molajo_Note: Testing added to ensure status change before onContentChangeState event is plugined  **/
        if ($this->existing_status == $validData->status || $this->isNew) {
        } else {
            /** Event: onContentChangeState **/
            $this->dispatcher->plugin('onContentChangeState', array($context, $this->get('id'), $validData->status));
        }

        /** Version_History: maintain count **/
        $results = $this->maintainVersionCount($context);
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** Plugin_Event: onContentAfterSave **/
        $this->dispatcher->plugin('onContentAfterSave', array($context, $validData, $this->isNew));

        /** Model: postSaveHook **/
        $this->postSaveHook($this->model, $validData);

        /** Cache: clear cache **/
//        $results = $this->cleanCache();

        /** success **/
        if ($this->getTask() == 'copy' || $this->getTask() == 'move') {
            return true;
        }

        if ($action == 'restore') {
            $this->redirectClass->setRedirectMessage($this->language->translate('RESTORE_SUCCESSFUL'));
        } else {
            $this->redirectClass->setRedirectMessage($this->language->translate('SAVE_SUCCESSFUL'));
        }

        JRequest::setVar('id', $this->get('id'));
        $this->redirectClass->setRedirectMessageType('message');

        return $this->redirectClass->setSuccessIndicator(true);
    }

    /**
     * deleteItem
     *
     * deletes individual items
     *
     * @return boolean
     * @since    1.0
     */
    public function deleteItem()
    {
        JRequest::checkToken() or die;

        /** initialisation */
        $results = parent::initialise('delete');
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** Preparation: Save form or version data **/
        $context = $this->row['option'] . '.' . JRequest::getCmd('view') . '.' . JRequest::getCmd(
                'view'
            ) . '.' . 'delete';

        /** only trashed and version items can be deleted **/
        if ($this->model->status == STATUS_TRASHED || $this->model->status == STATUS_VERSION) {
        } else {
//ERROR_VERSION_SAVE_FAILED
            return false;
        }

        /** Version_history: see if version needed **/
        $results = $this->createVersion($context);
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** Delete_Event: onContentBeforeDelete **/
        $results = $this->dispatcher->plugin('onContentBeforeDelete', array($context, $this->model));
        if (in_array(false, $results, true)) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** Model: delete **/
        $results = $this->model->delete($this->get('id'));
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** Version_history: maintain versions **/
        $results = $this->maintainVersionCount($context);
        if ($results === false) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** Delete_Event: onContentAfterDelete **/
        $results = $this->dispatcher->plugin('onContentAfterDelete', array($context, $this->model));
        if (in_array(false, $results, true)) {
            return $this->redirectClass->setSuccessIndicator(false);
        }

        /** clear cache **/
        $results = $this->cleanCache();

        /** success **/

        return true;
    }
}