
namespace App\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class {{$pascalCase}}Collection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection;
    }
}