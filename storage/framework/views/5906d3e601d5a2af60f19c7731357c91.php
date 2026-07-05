<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Masuk'); ?> &mdash; <?php echo e(config('app.name')); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('css/theme.css')); ?>">
</head>

<body class="hold-transition <?php echo $__env->yieldContent('body_class', 'login-page'); ?>">
    <?php echo $__env->yieldContent('content'); ?>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
</body>

</html><?php /**PATH C:\Users\USer\Website_Surat_Menyurat_Ngadri\resources\views/layouts/guest.blade.php ENDPATH**/ ?>