<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        schema::create('vcs_platforms', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('userId');
            $table->string('vcs')->comment('vcs 名称');
            $table->string('access_token')->nullable()->comment('访问 accessToken');
            $table->string('oauth_user_id')->nullable()->comment('oauth 用户ID');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('团队的链接 vcs 平台');
        });

        schema::create('build_package_jobs', function (Blueprint $table) {
            $table->id();
            $table->integer('package_id')->comment('包ID');
            $table->string('package_name')->nullable()->comment('包名');
            $table->string('tag')->comment('打包哪个 tag 版本');
            $table->integer('status')->comment('状态:0=未开始,1=进行中,2=成功,3=失败');
            $table->integer('user_id')->comment('提交人ID');
            $table->text('output')->comment('输出信息')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->comment('团队 package 打包任务');
        });

        Schema::create('package_tags', function (Blueprint $table) {
            $table->id();
            $table->integer('package_id')->comment('package id');
            $table->string('name')->comment('tag 版本');
            $table->string('description', 5000)->comment('描述');
            $table->string('type')->comment('类型');
            $table->string('dist')->comment('源码文件 zip');
            $table->integer('download_times')->comment('下载次数')->default(0);
            $table->json('require')->comment('依赖');
            $table->json('authors')->comment('包开发者');
            $table->timestamps();
            $table->softDeletes();

            $table->comment('团队包的 tag 列表');
        });

        schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->integer('vcs_id')->comment('链接授权的vsc平台');
            $table->integer('user_id')->comment('包添加人');
            $table->string('name')->comment('包名称');
            $table->string('repo_name')->comment('仓库名称');
            $table->string('url')->comment('包的仓库地址');
            $table->string('stay_at',10)->nullable()->comment('仓库保存在哪个平台.github/gitee.');
            $table->tinyInteger('status')->default(1)->comment('状态: 0=关闭,1=开启');
            $table->string('description', 1000)->comment('仓库描述')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->comment('packages');
        });

        Schema::create('license_packages', function (Blueprint $table) {
            $table->id();
            $table->string('license_id')->comment('license_id');
            $table->integer('package_id')->comment('package_id');
            $table->timestamps();

            $table->comment('license关联的packages');
        });

        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('名称');
            $table->char('license', 32)->unique()->comment('license');
            $table->string('type',10)->comment('类型');
            $table->unsignedBigInteger('expired_at')->nullable()->comment('到期时间');
            $table->integer('user_id')->comment('创建人ID');
            $table->tinyInteger('status')->default(1)->comment('状态:0=关闭,1=开启');
            $table->timestamps();
            $table->softDeletes();

            $table->comment('授权码');
        });

        Schema::create('license_users', function (Blueprint $table) {
            $table->id();
            $table->integer('license_id')->comment('许可证ID');
            $table->string('email', 100)->unique()->comment('邮箱');
            $table->char('license', 32)->comment('用户认证的 License');
            $table->integer('allow_ip_number')->default(1)->comment('允许IP数量');
            $table->json('ip_address')->nullable()->comment('ip集合,主要记录用户获取包 ip 来源');
            $table->tinyInteger('status')->default(1)->comment('状态');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['email', 'license']);
            $table->comment('用户授权码关联');
        });

        Schema::create('package_download', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->comment('包ID');
            $table->string('user_id')->comment('下载用户ID');
            $table->string('package_name', 100)->comment('包名');
            $table->string('version', 100)->comment('版本');
            $table->string('ip', 100)->comment('下载IP地址');
            $table->string('source', 100)->comment('下载代码包');
            $table->timestamps();

            $table->comment('包下载');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('vcs_platforms');
        Schema::dropIfExists('build_package_jobs');
        Schema::dropIfExists('package_tags');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('license_packages');
        Schema::dropIfExists('licenses');
        Schema::dropIfExists('license_users');
        Schema::dropIfExists('package_download');
    }
};
