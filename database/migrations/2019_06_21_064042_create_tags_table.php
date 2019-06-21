<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->string('tag');
            $table->integer('tag_id');

            $table->primary('tag');
        });

        //Insert tags
        $tags = array(
            array('tag' => 'Clean List','tag_id' => '159'),
            array('tag' => 'Duplicate Contacts','tag_id' => '163'),
            array('tag' => 'Email Typos','tag_id' => '161'),
            array('tag' => 'Module reminders completed','tag_id' => '154'),
            array('tag' => 'Start IAA Module 1 Reminders','tag_id' => '138'),
            array('tag' => 'Start IAA Module 2 Reminders','tag_id' => '140'),
            array('tag' => 'Start IAA Module 3 Reminders','tag_id' => '142'),
            array('tag' => 'Start IAA Module 4 Reminders','tag_id' => '144'),
            array('tag' => 'Start IAA Module 5 Reminders','tag_id' => '146'),
            array('tag' => 'Start IAA Module 6 Reminders','tag_id' => '148'),
            array('tag' => 'Start IAA Module 7 Reminders','tag_id' => '150'),
            array('tag' => 'Start IEA Module 1 Reminders','tag_id' => '124'),
            array('tag' => 'Start IEA Module 2 Reminders','tag_id' => '126'),
            array('tag' => 'Start IEA Module 3 Reminders','tag_id' => '128'),
            array('tag' => 'Start IEA Module 4 Reminders','tag_id' => '130'),
            array('tag' => 'Start IEA Module 5 Reminders','tag_id' => '132'),
            array('tag' => 'Start IEA Module 6 Reminders','tag_id' => '134'),
            array('tag' => 'Start IEA Module 7 Reminders','tag_id' => '136'),
            array('tag' => 'Start IPA Module 1 Reminders','tag_id' => '110'),
            array('tag' => 'Start IPA Module 2 Reminders','tag_id' => '112'),
            array('tag' => 'Start IPA Module 3 Reminders','tag_id' => '114'),
            array('tag' => 'Start IPA Module 4 Reminders','tag_id' => '116'),
            array('tag' => 'Start IPA Module 5 Reminders','tag_id' => '118'),
            array('tag' => 'Start IPA Module 6 Reminders','tag_id' => '120'),
            array('tag' => 'Start IPA Module 7 Reminders','tag_id' => '122')
        );

        DB::table('tags')->insert($tags);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}
