<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago - <?= e($recibo['numero_recibo']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; }
        .recibo-info { margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .total { font-size: 18px; font-weight: bold; text-align: right; margin-top: 20px; }
        .firma { margin-top: 80px; text-align: center; border-top: 1px solid #000; width: 300px; margin-left: auto; margin-right: auto; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= e($config['nombre_colegio'] ?? 'Colegio Profesional') ?></h1>
        <p>RUC: <?= e($config['ruc_colegio'] ?? '') ?></p>
        <p><?= e($config['direccion_colegio'] ?? '') ?></p>
        <p>Tel: <?= e($config['telefono_colegio'] ?? '') ?></p>
        <h2>RECIBO DE PAGO</h2>
        <h3>N° <?= e($recibo['numero_recibo']) ?></h3>
    </div>

    <div class="recibo-info">
        <p><strong>Fecha:</strong> <?= formatDate($recibo['fecha_emision'], 'd/m/Y H:i') ?></p>
        <p><strong>Recibido de:</strong> <?= e($recibo['apellido_paterno'] . ' ' . $recibo['apellido_materno'] . ', ' . $recibo['nombres']) ?></p>
        <p><strong>DNI:</strong> <?= e($recibo['dni']) ?></p>
        <p><strong>Método de Pago:</strong> <?= e($recibo['metodo_pago']) ?></p>
        <?php if ($recibo['numero_operacion']): ?>
            <p><strong>N° Operación:</strong> <?= e($recibo['numero_operacion']) ?></p>
        <?php endif; ?>
    </div>

    <h3>Detalle de Cuotas Pagadas</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Concepto</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cuotas as $cuota): ?>
                <tr>
                    <td>Cuota <?= \Models\Cuota::getNombreMes($cuota['mes']) ?> <?= $cuota['anio'] ?></td>
                    <td>S/. <?= number_format($cuota['monto_aplicado'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total">
        TOTAL: <?= formatMoney($recibo['monto_total']) ?>
    </div>

    <?php if ($recibo['observaciones']): ?>
        <p><strong>Observaciones:</strong> <?= e($recibo['observaciones']) ?></p>
    <?php endif; ?>

    <div class="firma">
        <p><?= e($recibo['cajero']) ?><br>Cajero</p>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
