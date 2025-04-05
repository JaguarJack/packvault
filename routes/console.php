<?php

use Illuminate\Support\Facades\Schedule;

# 刷新 gitee access token
Schedule::command('update:gitee:access:token')->runInBackground()->hourly();
