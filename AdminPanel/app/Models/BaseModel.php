<?php
/**
 * By MahmoudAp
 * Github: https://github.com/mahmoud-ap
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    public $cont;
    public function __construct($cont = null)
    {
        db();
        
        $this->timestamps = false;
        if (!empty($cont)) {
            $this->cont = $cont;
        }
    }

}
