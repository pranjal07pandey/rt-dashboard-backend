<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocumentTheme;

class DocumentThemeRepository implements IRepository
{
    public function getModel()
    {
        return new DocumentTheme();
    }

    public function getDataById($request = null)
    {
        return $this->getModel()->find($request->id);
    }

    public function getDataWhere($array = [])
    {
        return $this->getModel()->where($array);
    }

    public function insertAndUpdate($request = null)
    {
        if ($request->has('document_theme_id')) {
            $document_theme = $this->getModel()->find($request->document_theme__id);
        } else {
            $document_theme = $this->getModel();
        }

        (!$request->has('name'))?:                                      $document_theme->name   = $request->name;
        (!$request->has('description'))?:                               $document_theme->description  = $request->description;
        (!$request->has('type'))?:                                      $document_theme->type  = $request->type;
        (!$request->has('preview'))?:                                   $document_theme->preview  = $request->preview;
        (!$request->has('web_view_path'))?:                             $document_theme->web_view_path  = $request->web_view_path;
        (!$request->has('pdf_view_path'))?:                             $document_theme->pdf_view_path  = $request->pdf_view_path;
        (!$request->has('mobile_view_path'))?:                          $document_theme->mobile_view_path  = $request->mobile_view_path;
        (!$request->has('is_active'))?:                                 $document_theme->is_active  = $request->is_active;
        (!$request->has('paid_free'))?:                                 $document_theme->paid_free  = $request->paid_free;
        (!$request->has('price'))?:                                     $document_theme->price  = $request->price;
        (!$request->has('slug'))?:                                      $document_theme->slug  = $request->slug;
        (!$request->has('screenshot'))?:                                $document_theme->screenshot  = $request->screenshot;

        $document_theme->save();
        return $document_theme;
    }

    public function deleteDataById($request = null)
    {
        $document_theme = $this->getModel()->find($request->id);
        $document_theme->delete();
        return $document_theme;
    }
}
