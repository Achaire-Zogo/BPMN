<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessInstancesTable extends Migration
{
    public function up()
    {
        Schema::create('process_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bpmn_diagram_id')->constrained('b_p_m_n_diagrams')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->string('current_task')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('process_instances');
    }
}
