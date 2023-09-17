<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFichasPerguntasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fichas_perguntas', function(Blueprint $table) {
            $table->increments('id');

            $table->string('categoria');
            $table->string('nome_pergunta');
            $table->string('helper')->nullable();
            $table->boolean('ativo')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('fichas_perguntas')->insert(['categoria' => '1. Vacinação',                   'nome_pergunta' => 'Em dia?']);

        DB::table('fichas_perguntas')->insert(['categoria' => '2. Sistema Reprodutivo',         'nome_pergunta' => 'Paciente é castrado?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '2. Sistema Reprodutivo',         'nome_pergunta' => 'Já cruzou?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '2. Sistema Reprodutivo',         'nome_pergunta' => 'Se fêmea, há chances de estar prenhe?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '2. Sistema Reprodutivo',         'nome_pergunta' => 'Se fêmea, apresenta algum nódulo ou massa em cadeia mamária?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '2. Sistema Reprodutivo',         'nome_pergunta' => 'Se fêmea, apresenta sinais de pseudociese?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '2. Sistema Reprodutivo',         'nome_pergunta' => 'Se fêmea, apresenta alguma secreção em região vulvar?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '2. Sistema Reprodutivo',         'nome_pergunta' => 'Se macho, é criptorquida?']);

        DB::table('fichas_perguntas')->insert(['categoria' => '3. Sistema Digestivo',           'nome_pergunta' => 'Se alimentando, defecando e urinando normalmente?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '3. Sistema Digestivo',           'nome_pergunta' => 'Histórico de gastroenterite?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '3. Sistema Digestivo',           'nome_pergunta' => 'Já realizou alguma cirurgia digestiva?']);

        DB::table('fichas_perguntas')->insert(['categoria' => '4. Sistema Cardio-Respiratório', 'nome_pergunta' => 'Possui alguma patologia cardíaca? Sopro? Arritmia?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '4. Sistema Cardio-Respiratório', 'nome_pergunta' => 'Apresenta sinais de tosse intermitente ou recorrente?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '4. Sistema Cardio-Respiratório', 'nome_pergunta' => 'Intolerância à exercícios?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '4. Sistema Cardio-Respiratório', 'nome_pergunta' => 'Há diagnóstico de colapso de traqueia?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '4. Sistema Cardio-Respiratório', 'nome_pergunta' => 'Alguma alteração digna de nota?']);

        DB::table('fichas_perguntas')->insert(['categoria' => '5. Sistema Gênito-Urinário',     'nome_pergunta' => 'Sinais de hematúria, cálculos, obstruções?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '5. Sistema Gênito-Urinário',     'nome_pergunta' => 'Alguma alteração digna de nota?']);

        DB::table('fichas_perguntas')->insert(['categoria' => '6. Sistema Locomotor-Nervoso',   'nome_pergunta' => 'Sinais de claudicação? Alteração em marcha?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '6. Sistema Locomotor-Nervoso',   'nome_pergunta' => 'Já realizou alguma cirurgia ortopédica?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '6. Sistema Locomotor-Nervoso',   'nome_pergunta' => 'Alguma patologia em locomotor já diagnosticada?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '6. Sistema Locomotor-Nervoso',   'nome_pergunta' => 'Sinais de dor em coluna?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '6. Sistema Locomotor-Nervoso',   'nome_pergunta' => 'Sinais de convulsões ou síncope?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '6. Sistema Locomotor-Nervoso',   'nome_pergunta' => 'Alguma outra alteração digna de nota?']);

        DB::table('fichas_perguntas')->insert(['categoria' => '7. Sistema Tegumentar',          'nome_pergunta' => 'Paciente atópico?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '7. Sistema Tegumentar',          'nome_pergunta' => 'Presença de nódulos ou massas?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '7. Sistema Tegumentar',          'nome_pergunta' => 'Alguma alteração dermatológica digna de nota?']);

        DB::table('fichas_perguntas')->insert(['categoria' => '8. Sistema Oftálmico',           'nome_pergunta' => 'Secreção ocular?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '8. Sistema Oftálmico',           'nome_pergunta' => 'Sinal de obstrução de ducto nasolacrimal?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '8. Sistema Oftálmico',           'nome_pergunta' => 'Alguma alteração digna de nota?']);

        DB::table('fichas_perguntas')->insert(['categoria' => '9. Histórico',                   'nome_pergunta' => 'Já levou seu pet no veterinário por algum motivo?']);
        DB::table('fichas_perguntas')->insert(['categoria' => '9. Histórico',                   'nome_pergunta' => 'Há pré-existência?', 'helper' => 'São patologias (com ou sem sintomatologia) que o pet possui no momento da microchipagem.']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('fichas_perguntas');
    }
}
