use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create{{ucfirst($plural)}} extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('{{$plural}}', function(Blueprint $table) {
            @php
                /** @var \App\Console\Commands\DataType $dataType */
            @endphp
            @foreach ($dataTypes as $dataType)
                $table->{{$dataType->getType()}}('{{$dataType->getName()}}') @if($dataType->isNullable())->nullable() @endif;
            @endforeach

            $table->timestampsTz();

            @if ($shouldRenderPrimaryKeys)
                $table->primary([
                @foreach ($primaryIdDataTypes as $dataType)
                    '{{$dataType->getName()}}',
                @endforeach
                ]);
            @endif

            @if ($implementSoftDeletes)
                $table->softDeletes();
            @endif
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('{{$plural}}');
    }
}