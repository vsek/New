<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Form;

/**
 * Description of NewPresenter
 *
 * @author vsek
 */
class NewPresenter extends BasePresenterM{
    /** @var \App\Model\Module\News @inject */
    public $model;
    
    /**
     *
     * @var \Nette\Database\Table\ActiveRow
     */
    private $row = null;
    
    public function submitFormEdit(Form $form){
        $values = $form->getValues();
        if($values['link'] == ''){
            $link = \Nette\Utils\Strings::webalize($values['name']);
        }else{
            $link = \Nette\Utils\Strings::webalize($values['link']);
        }
        $item = $this->model->where('link', $values['link'])
                ->where('NOT id', $this->row['id'])
                ->fetch();
        if($item){
            $form['link']->addError($this->translator->translate('new.linkExist'));
        }else{
            $data = array(
                'name' => $values['name'],
                'link' => $link,
                'perex' => $values['perex'] == '' ? null : $values['perex'],
                'image' => $values['image'] == '' ? null : $values['image'],
                'text' => $values['text'],
            );
            $this->row->update($data);

            $this->flashMessage($this->translator->translate('admin.form.editSuccess'));
            $this->redirect('edit', $this->row->id);
        }
    }
    
    private function exist($id){
        $this->row = $this->model->get($id);
        if(!$this->row){
            $this->flashMessage($this->translator->translate('admin.text.notitemNotExist'), 'error');
            $this->redirect('default');
        }
    }
    
    protected function createComponentFormEdit($name){
        $form = new Form($this, $name);
        
        $form->addText('name', $this->translator->translate('new.name'))
                ->addRule(Form::FILLED, $this->translator->translate('admin.form.isRequired'));
        $form->addText('link', $this->translator->translate('new.link'));
        $form->addUpload('image', $this->translator->translate('new.image'));
        $form->addTextArea('perex', $this->translator->translate('new.perex'));
        $form->addSpawEditor('text', $this->translator->translate('new.text'));
        
        $form->addSubmit('send', $this->translator->translate('admin.form.edit'));
        
        $form->onSuccess[] = [$this, 'submitFormEdit'];
        
        $form->setDefaults(array(
            'name' => $this->row->name,
            'link' => $this->row->link,
            'perex' => $this->row->perex,
            'text' => $this->row->text,
            'image' => $this->row->image,
        ));
        
        return $form;
    }
    
    public function actionEdit($id){
        $this->exist($id);
    }
    
    public function actionDelete($id){
        $this->exist($id);
        $this->row->delete();
        $this->flashMessage($this->translator->translate('admin.text.itemDeleted'));
        $this->redirect('default');
    }
    
    public function submitFormNew(Form $form){
        $values = $form->getValues();

        if($values->link == ''){
            $link = \Nette\Utils\Strings::webalize($values->name);
        }else{
            $link = \Nette\Utils\Strings::webalize($values->link);
        }
        
        $item = $this->model->where('link', $link)->fetch();
        if($item){
            $form['link']->addError($this->translator->translate('new.linkExist'));
        }else{
            $this->model->insert(array(
                'name' => $values['name'],
                'link' => $link,
                'perex' => $values['perex'] == '' ? null : $values['perex'],
                'image' => $values['image'] == '' ? null : $values['image'],
                'text' => $values['text'],
            ));

            $this->flashMessage($this->translator->translate('admin.text.inserted'));
            $this->redirect('default');
        }
    }
    
    protected function createComponentFormNew($name){
        $form = new Form($this, $name);
        
        $form->addText('name', $this->translator->translate('new.name'))
                ->addRule(Form::FILLED, $this->translator->translate('admin.form.isRequired'));
        $form->addText('link', $this->translator->translate('new.link'));
        $form->addUpload('image', $this->translator->translate('new.image'));
        $form->addTextArea('perex', $this->translator->translate('new.perex'));
        $form->addSpawEditor('text', $this->translator->translate('new.text'));
        
        $form->addSubmit('send', $this->translator->translate('admin.form.create'));
        
        $form->onSuccess[] = [$this, 'submitFormNew'];
        
        return $form;
    }
    
    protected function createComponentGrid(){
        $grid = new \App\Grid\Grid();

        $grid->setModel($this->model->getAll());
        $grid->addColumn(new \App\Grid\Column\Column('name', $this->translator->translate('new.name')));
        $grid->addColumn(new \App\Grid\Column\Column('link', $this->translator->translate('new.link')));
        $grid->addColumn(new \App\Grid\Column\Column('id', $this->translator->translate('admin.grid.id')));
        
        $grid->addMenu(new \App\Grid\Menu\Update('edit', $this->translator->translate('admin.form.edit')));
        $grid->addMenu(new \App\Grid\Menu\Delete('delete', $this->translator->translate('admin.grid.delete')));
        
        $grid->setOrder('created');
        
        return $grid;
    }
}