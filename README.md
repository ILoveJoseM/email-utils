# 发送邮件说明
## 发送邮件

- 开启队列消费者
php artisan queue:listen emails

- 向队列中写发送的内容
php artisan email:send

> 脚本最好设置为定时任务去检查，建议每分钟执行