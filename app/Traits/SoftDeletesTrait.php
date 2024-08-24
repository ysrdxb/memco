<?php 

namespace App\Traits;

trait SoftDeletesTrait
{
    public function softDelete($id)
    {
        $model = $this->find($id);
        if ($model) {
            $model->delete();
            return true;
        }
        return false;
    }

    public function restoreSoftDelete($id)
    {
        $model = $this->withTrashed()->find($id);
        if ($model) {
            $model->restore();
            return true;
        }
        return false;
    }
}