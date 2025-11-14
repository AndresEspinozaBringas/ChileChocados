<?php  // phpcs:ignore PSR12.Files.FileHeader.SpacingAfterTagBlock, PSR12.Files.FileHeader.SpacingAfterTagBlock, PSR12.Files.FileHeader.SpacingAfterTagBlock

/**
 * Dashboard de Reportes - Diseño UX/UI Optimizado
 * Enfoque: Insights accionables y toma de decisiones
 */
layout('header');
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin-layout.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
/* Variables de diseño */
:root {
    --color-primary: #E6332A;
    --color-success: #10B981;
    --color-warning: #F59E0B;
    --color-danger: #EF4444;
    --color-info: #3B82F6;
    --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
    --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
}

/* Layout mejorado */
.dashboard-grid {
    display: grid;
    gap: 24px;
    margin-bottom: 32px;
}

/* KPI Cards con insights */
.kpi-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-md);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--accent-color);
}

.kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.kpi-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.kpi-value {
    font-size: 36px;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 8px;
    color: #111827;
}

.kpi-label {
    font-size: 14px;
    color: #6B7280;
    font-weight: 500;
    margin-bottom: 12px;
}

.kpi-insight {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    padding: 8px 12px;
    border-radius: 8px;
    font-weight: 600;
}

.insight-positive {
    background: #D1FAE5;
    color: #065F46;
}

.insight-negative {
    background: #FEE2E2;
    color: #991B1B;
}

.insight-neutral {
    background: #F3F4F6;
    color: #374151;
}

/* Chart containers mejorados */
.chart-section {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: var(--shadow-md);
    margin-bottom: 24px;
}

.chart-header {
    margin-bottom: 24px;
}

.chart-title {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chart-subtitle {
    font-size: 14px;
    color: #6B7280;
    line-height: 1.5;
}

.chart-container {
    position: relative;
    height: 320px;
}

/* Tabla mejorada */
.data-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.data-table thead th {
    background: #F9FAFB;
    padding: 12px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 2px solid #E5E7EB;
}

.data-table tbody td {
    padding: 16px;
    border-bottom: 1px solid #F3F4F6;
    font-size: 14px;
}

.data-table tbody tr:hover {
    background: #F9FAFB;
}

/* Responsive */
@media (max-width: 1024px) {
    .dashboard-grid.cols-4 {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .grid.cols-2 {
        grid-template-columns: 1fr !important;
    }
    
    .dashboard-grid.cols-2 {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 768px) {
    .admin-container,
    .container {
        padding: 16px !important;
    }
    
    /* Header */
    main > div:first-child h1 {
        font-size: 24px !important;
        flex-direction: column;
        align-items: flex-start !important;
        gap: 8px !important;
    }
    
    main > div:first-child p {
        font-size: 13px !important;
    }
    
    /* Dashboard grid */
    .dashboard-grid.cols-4,
    .dashboard-grid {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
    }
    
    .grid.cols-2 {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
    }
    
    /* KPI Cards */
    .kpi-card {
        padding: 20px;
    }
    
    .kpi-icon {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }
    
    .kpi-value {
        font-size: 28px;
    }
    
    .kpi-label {
        font-size: 13px;
    }
    
    .kpi-insight {
        font-size: 12px;
        padding: 6px 10px;
    }
    
    /* Chart sections - MÁS RIGUROSO */
    .chart-section {
        padding: 16px !important;
        margin-bottom: 16px;
        overflow: hidden;
    }
    
    .chart-header {
        margin-bottom: 20px !important;
    }
    
    .chart-title {
        font-size: 15px !important;
        flex-direction: column;
        align-items: flex-start !important;
        gap: 6px !important;
        line-height: 1.4 !important;
    }
    
    .chart-subtitle {
        font-size: 12px !important;
        line-height: 1.5 !important;
    }
    
    /* ALTURA AUMENTADA Y PADDING PARA GRÁFICOS */
    .chart-container {
        height: 300px !important;
        padding: 10px 0 !important;
        margin: 0 !important;
        position: relative !important;
    }
    
    /* Contenedor específico para gráficos lado a lado */
    .dashboard-grid.cols-2 .chart-container {
        height: 280px !important;
        padding: 10px 0 20px 0 !important;
    }
    
    /* Canvas responsive */
    .chart-container canvas {
        max-width: 100% !important;
        max-height: 100% !important;
    }
    
    /* Tabla */
    .data-table {
        font-size: 12px;
    }
    
    .data-table thead th {
        padding: 10px 12px;
        font-size: 11px;
    }
    
    .data-table tbody td {
        padding: 12px;
    }
    
    /* Ocultar columnas menos importantes en móvil */
    .data-table thead th:nth-child(6),
    .data-table tbody td:nth-child(6) {
        display: none;
    }
}

@media (max-width: 640px) {
    .kpi-value {
        font-size: 24px;
    }
    
    .kpi-header {
        flex-direction: column;
        gap: 12px;
    }
    
    /* ALTURA MAYOR PARA PANTALLAS PEQUEÑAS */
    .chart-container {
        height: 280px !important;
        padding: 10px 0 25px 0 !important;
    }
    
    /* Gráficos en grid necesitan más espacio */
    .dashboard-grid.cols-2 .chart-container {
        height: 260px !important;
        padding: 10px 0 30px 0 !important;
    }
    
    /* Tabla responsive - scroll horizontal */
    .chart-section > div[style*="overflow-x: auto"] {
        margin: 0 -16px;
        padding: 0 16px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .data-table {
        min-width: 700px;
        display: table;
    }
    
    .data-table thead th,
    .data-table tbody td {
        white-space: nowrap;
    }
    
    /* Hacer la tabla más compacta */
    .data-table thead th {
        padding: 8px 10px;
        font-size: 10px;
    }
    
    .data-table tbody td {
        padding: 10px;
        font-size: 12px;
    }
    
    .data-table tbody td code {
        font-size: 11px;
        padding: 2px 6px;
    }
    
    .data-table tbody td small {
        font-size: 11px;
    }
    
    .data-table tbody td strong {
        font-size: 13px;
    }
    
    /* Badges más pequeños */
    .data-table .badge {
        font-size: 10px;
        padding: 3px 8px;
    }
}

/* Estilos adicionales para mejorar la visualización de gráficos en móvil */
@media (max-width: 768px) {
    /* Asegurar que los canvas de gráficos sean responsive */
    canvas {
        max-width: 100% !important;
        height: auto !important;
        display: block !important;
    }
    
    /* Ajustar el contenedor de gráficos lado a lado */
    .dashboard-grid.cols-2 > .chart-section {
        width: 100%;
    }
    
    .dashboard-grid.cols-2 > .chart-section .chart-container {
        min-height: 280px !important;
        height: 280px !important;
    }
    
    /* Mejorar legibilidad de etiquetas en gráficos */
    .chart-section canvas {
        font-size: 10px !important;
    }
}

/* Estilos para scroll horizontal suave en tablas */
.chart-section > div[style*="overflow-x"] {
    scrollbar-width: thin;
    scrollbar-color: #CBD5E0 #F7FAFC;
}

.chart-section > div[style*="overflow-x"]::-webkit-scrollbar {
    height: 8px;
}

.chart-section > div[style*="overflow-x"]::-webkit-scrollbar-track {
    background: #F7FAFC;
    border-radius: 4px;
}

.chart-section > div[style*="overflow-x"]::-webkit-scrollbar-thumb {
    background: #CBD5E0;
    border-radius: 4px;
}

.chart-section > div[style*="overflow-x"]::-webkit-scrollbar-thumb:hover {
    background: #A0AEC0;
}

/* Cards de transacciones para móvil */
.transaction-card {
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
}

.transaction-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #E5E7EB;
}

.transaction-card-body {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.transaction-card-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
}

.transaction-label {
    font-weight: 600;
    color: #6B7280;
}

.transaction-value {
    font-weight: 500;
    color: #111827;
    text-align: right;
}

/* Mostrar/ocultar vistas según el tamaño de pantalla */
@media (min-width: 769px) {
    .transactions-cards-view {
        display: none !important;
    }
    
    .transactions-table-view {
        display: block !important;
    }
}

@media (max-width: 768px) {
    .transactions-table-view {
        display: none !important;
    }
    
    .transactions-cards-view {
        display: block !important;
    }
}

/* Animaciones */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.kpi-card {
    animation: slideUp 0.4s ease-out;
}

.kpi-card:nth-child(1) { animation-delay: 0.1s; }
.kpi-card:nth-child(2) { animation-delay: 0.2s; }
.kpi-card:nth-child(3) { animation-delay: 0.3s; }
.kpi-card:nth-child(4) { animation-delay: 0.4s; }

/* ============================================================================
 * DARK MODE
 * ============================================================================ */

:root[data-theme="dark"] .kpi-card {
  background: #1F2937;
  box-shadow: var(--shadow-md);
  border: 1px solid #374151;
}

:root[data-theme="dark"] .kpi-card:hover {
  box-shadow: var(--shadow-lg);
  border-color: #4B5563;
}

:root[data-theme="dark"] .kpi-card::before {
  background: var(--accent-color);
}

:root[data-theme="dark"] .kpi-icon {
  /* Los iconos mantienen sus colores de fondo específicos */
}

:root[data-theme="dark"] .chart-section {
  background: #1F2937;
  box-shadow: var(--shadow-md);
  border: 1px solid #374151;
}

:root[data-theme="dark"] .data-table thead th {
  background: #111827;
  border-bottom-color: #374151;
}

:root[data-theme="dark"] .data-table tbody td {
  border-bottom-color: #374151;
}

:root[data-theme="dark"] .data-table tbody tr:hover {
  background: #374151;
}

:root[data-theme="dark"] .transaction-card {
  background: #1F2937;
  border-color: #374151;
}

:root[data-theme="dark"] .transaction-card-header {
  border-bottom-color: #374151;
}

/* Canvas de gráficos - Light Mode */
canvas {
  background: #FFFFFF;
  border-radius: 8px;
}

.chart-section canvas {
  background: #FFFFFF;
}

.chart-container canvas {
  background: #FFFFFF;
}

/* Canvas de gráficos - Dark Mode */
:root[data-theme="dark"] canvas {
  background: #1F2937 !important;
  border-radius: 8px;
}

:root[data-theme="dark"] .chart-section canvas {
  background: #1F2937 !important;
}

:root[data-theme="dark"] .chart-container canvas {
  background: #1F2937 !important;
}

/* Elementos con color hardcodeado - Dark Mode */
:root[data-theme="dark"] [style*="color: #999"],
:root[data-theme="dark"] [style*="color:#999"] {
  color: #9CA3AF !important;
}

:root[data-theme="dark"] div[style*="text-align: center"][style*="padding"] {
  color: #D1D5DB;
}

/* Títulos de gráficas - Dark Mode */
:root[data-theme="dark"] .chart-title {
  color: #F3F4F6 !important;
}

:root[data-theme="dark"] .chart-subtitle {
  color: #9CA3AF !important;
}

:root[data-theme="dark"] .kpi-value {
  color: #F3F4F6 !important;
}

:root[data-theme="dark"] .kpi-label {
  color: #9CA3AF !important;
}

:root[data-theme="dark"] .chart-header h2,
:root[data-theme="dark"] .chart-header h3 {
  color: #F3F4F6 !important;
}

:root[data-theme="dark"] .transaction-label {
  color: #9CA3AF !important;
}

:root[data-theme="dark"] .transaction-value {
  color: #F3F4F6 !important;
}
</style>

<main class="container admin-container">

  <!-- Header con contexto -->
  <div style="margin-bottom: 32px;">
    <h1 class="h1" style="margin-bottom: 8px; display: flex; align-items: center; gap: 12px;">
      <?php echo icon('bar-chart-2', 32); ?>
      Dashboard de Reportes
    </h1>
    <p class="meta" style="font-size: 15px;">
      Métricas clave para la toma de decisiones • Actualizado: <?php echo date('d/m/Y H:i'); ?>
    </p>
  </div>

  <!-- KPIs Principales con Insights -->
  <div class="dashboard-grid cols-4" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
    
    <?php
    // Calcular insights
    $totalUsuarios = $this->db->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
    $usuariosUltimoMes = $this->db->query('SELECT COUNT(*) FROM usuarios WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL 1 MONTH)')->fetchColumn();
    $crecimientoUsuarios = $totalUsuarios > 0 ? round(($usuariosUltimoMes / $totalUsuarios) * 100, 1) : 0;

    $totalPublicaciones = $this->db->query("SELECT COUNT(*) FROM publicaciones WHERE estado != 'archivada'")->fetchColumn();
    $publicacionesActivas = $this->db->query("SELECT COUNT(*) FROM publicaciones WHERE estado = 'aprobada'")->fetchColumn();
    $tasaAprobacion = $totalPublicaciones > 0 ? round(($publicacionesActivas / $totalPublicaciones) * 100, 1) : 0;

    $totalMensajes = $this->db->query('SELECT COUNT(*) FROM mensajes')->fetchColumn();
    $mensajesHoy = $this->db->query('SELECT COUNT(*) FROM mensajes WHERE DATE(fecha_envio) = CURDATE()')->fetchColumn();

    $totalRecaudado = $estadisticasPagos->total_recaudado ?? 0;
    $pagosAprobados = $estadisticasPagos->pagos_aprobados ?? 0;
    $tasaConversion = ($estadisticasPagos->total_pagos ?? 0) > 0 ? round(($pagosAprobados / $estadisticasPagos->total_pagos) * 100, 1) : 0;
    ?>
    
    <!-- KPI: Usuarios -->
    <div class="kpi-card" style="--accent-color: #3B82F6;">
      <div class="kpi-header">
        <div>
          <div class="kpi-label">Total Usuarios</div>
          <div class="kpi-value"><?php echo number_format($totalUsuarios); ?></div>
        </div>
        <div class="kpi-icon" style="background: #DBEAFE; color: #3B82F6;">
          <?php echo icon('users', 32); ?>
        </div>
      </div>
      <div class="kpi-insight <?php echo $usuariosUltimoMes > 0 ? 'insight-positive' : 'insight-neutral'; ?>">
        <?php if ($usuariosUltimoMes > 0): ?>
          <?php echo icon('trending-up', 14); ?> +<?php echo $usuariosUltimoMes; ?> nuevos este mes
        <?php else: ?>
          <?php echo icon('minus', 14); ?> Sin nuevos registros este mes
        <?php endif; ?>
      </div>
    </div>

    <!-- KPI: Publicaciones -->
    <div class="kpi-card" style="--accent-color: #10B981;">
      <div class="kpi-header">
        <div>
          <div class="kpi-label">Publicaciones Activas</div>
          <div class="kpi-value"><?php echo number_format($publicacionesActivas); ?></div>
        </div>
        <div class="kpi-icon" style="background: #D1FAE5; color: #10B981;">
          <?php echo icon('file-text', 32); ?>
        </div>
      </div>
      <div class="kpi-insight <?php echo $tasaAprobacion >= 70 ? 'insight-positive' : 'insight-neutral'; ?>">
        <?php echo icon('check-circle', 14); ?> <?php echo $tasaAprobacion; ?>% tasa de aprobación
      </div>
    </div>

    <!-- KPI: Mensajes -->
    <div class="kpi-card" style="--accent-color: #F59E0B;">
      <div class="kpi-header">
        <div>
          <div class="kpi-label">Total Mensajes</div>
          <div class="kpi-value"><?php echo number_format($totalMensajes); ?></div>
        </div>
        <div class="kpi-icon" style="background: #FEF3C7; color: #F59E0B;">
          <?php echo icon('message-circle', 32); ?>
        </div>
      </div>
      <div class="kpi-insight insight-neutral">
        <?php echo icon('clock', 14); ?> <?php echo $mensajesHoy; ?> mensajes hoy
      </div>
    </div>

    <!-- KPI: Ingresos -->
    <div class="kpi-card" style="--accent-color: #10B981;">
      <div class="kpi-header">
        <div>
          <div class="kpi-label">Ingresos Totales</div>
          <div class="kpi-value" style="font-size: 28px;"><?php echo formatPrice($totalRecaudado); ?></div>
        </div>
        <div class="kpi-icon" style="background: #D1FAE5; color: #10B981;">
          <?php echo icon('dollar-sign', 32); ?>
        </div>
      </div>
      <div class="kpi-insight <?php echo $tasaConversion >= 80 ? 'insight-positive' : ($tasaConversion >= 50 ? 'insight-neutral' : 'insight-negative'); ?>">
        <?php if ($tasaConversion >= 80): ?>
          <?php echo icon('trending-up', 14); ?> <?php echo $tasaConversion; ?>% conversión (Excelente)
        <?php elseif ($tasaConversion >= 50): ?>
          <?php echo icon('minus', 14); ?> <?php echo $tasaConversion; ?>% conversión (Normal)
        <?php else: ?>
          <?php echo icon('trending-down', 14); ?> <?php echo $tasaConversion; ?>% conversión (Mejorable)
        <?php endif; ?>
      </div>
    </div>
    
    <!-- KPI: Visitas Totales -->
    <div class="kpi-card" style="--accent-color: #8B5CF6;">
      <div class="kpi-header">
        <div>
          <div class="kpi-label">Visitas Totales</div>
          <div class="kpi-value"><?php echo number_format($visitasTotales); ?></div>
        </div>
        <div class="kpi-icon" style="background: #EDE9FE; color: #8B5CF6;">
          <?php echo icon('eye', 32); ?>
        </div>
      </div>
      <div class="kpi-insight insight-neutral">
        <?php echo icon('activity', 14); ?> En publicaciones activas
      </div>
    </div>
    
    <!-- KPI: Destacadas Activas -->
    <div class="kpi-card" style="--accent-color: #F59E0B;">
      <div class="kpi-header">
        <div>
          <div class="kpi-label">Destacadas Activas</div>
          <div class="kpi-value"><?php echo number_format($destacadasActivas); ?></div>
        </div>
        <div class="kpi-icon" style="background: #FEF3C7; color: #F59E0B;">
          <?php echo icon('star', 32); ?>
        </div>
      </div>
      <div class="kpi-insight insight-neutral">
        <?php echo icon('eye', 14); ?> <?php echo number_format($visitasDestacadas); ?> visitas
      </div>
    </div>
  </div>

  <!-- Gráficos de Actividad (desde /admin) -->
  <div style="margin-bottom: 32px;">
    <h2 class="h2" style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
      <?php echo icon('trending-up', 24); ?> Actividad y Estadísticas
    </h2>
    
    <div class="grid cols-2" style="gap: 16px;">
      <!-- Gráfico de Actividad Semanal -->
      <div class="chart-section" style="margin-bottom: 0;">
        <div class="chart-header">
          <h3 class="chart-title" style="font-size: 18px;">
            <?php echo icon('activity', 20); ?>
            Publicaciones (Últimos 7 días)
          </h3>
        </div>
        <div class="chart-container" style="height: 250px;">
          <canvas id="chartActividadSemanal"></canvas>
        </div>
      </div>
      
      <!-- Gráfico de Distribución de Estados -->
      <div class="chart-section" style="margin-bottom: 0;">
        <div class="chart-header">
          <h3 class="chart-title" style="font-size: 18px;">
            <?php echo icon('pie-chart', 20); ?>
            Distribución por Estado
          </h3>
        </div>
        <div class="chart-container" style="height: 250px;">
          <canvas id="chartDistribucionEstados"></canvas>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Gráficos de Categorías y Subcategorías lado a lado -->
  <div style="margin-bottom: 32px;">
    <h2 class="h2" style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
      <?php echo icon('bar-chart-2', 24); ?> Análisis de Categorías
    </h2>
    
    <div class="dashboard-grid cols-2" style="grid-template-columns: 1fr 1.85fr; gap: 24px;">
      <!-- Gráfico de Categorías (35%) -->
      <div class="chart-section" style="margin-bottom: 0;">
        <div class="chart-header">
          <h3 class="chart-title" style="font-size: 18px;">
            <?php echo icon('bar-chart-2', 20); ?>
            Top 5 Categorías Más Populares
          </h3>
        </div>
        <div class="chart-container" style="height: 300px;">
          <canvas id="chartPorCategoria"></canvas>
        </div>
      </div>
      
      <!-- Gráfico de Subcategorías (65%) -->
      <div class="chart-section" style="margin-bottom: 0;">
        <div class="chart-header">
          <h3 class="chart-title" style="font-size: 18px;">
            <?php echo icon('bar-chart-2', 20); ?>
            Top 5 Subcategorías Más Populares
          </h3>
        </div>
        <div class="chart-container" style="height: 300px;">
          <canvas id="chartPorSubcategoria"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Sección: Funnel de Conversión (Storytelling) -->
  <div class="chart-section">
    <div class="chart-header">
      <h2 class="chart-title">
        <?php echo icon('filter', 20); ?> Funnel de Conversión
      </h2>
      <p class="chart-subtitle">
        <strong>Historia:</strong> De cada 100 usuarios, ¿cuántos publican y cuántos pagan por destacar?
      </p>
    </div>
    <div class="chart-container" style="height: 400px;">
      <canvas id="funnelChart"></canvas>
    </div>
  </div>

  <!-- Sección: Análisis de Crecimiento -->
  <div class="chart-section">
    <div class="chart-header">
      <h2 class="chart-title">
        <?php echo icon('trending-up', 20); ?> Análisis de Crecimiento
      </h2>
      <p class="chart-subtitle">
        <strong>Insight:</strong> Monitorea el crecimiento de usuarios y publicaciones para identificar tendencias.
      </p>
    </div>
    <div class="dashboard-grid cols-2" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));">
      <div class="chart-container">
        <canvas id="usuariosChart"></canvas>
      </div>
      <div class="chart-container">
        <canvas id="publicacionesChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Sección: Análisis de Ingresos -->
  <div class="chart-section">
    <div class="chart-header">
      <h2 class="chart-title">
        <?php echo icon('dollar-sign', 20); ?> Análisis de Ingresos
      </h2>
      <p class="chart-subtitle">
        <strong>Insight:</strong> Identifica patrones de ingresos y optimiza estrategias de monetización.
        <?php if ($pagosAprobados > 0): ?>
          Ticket promedio: <strong><?php echo formatPrice($estadisticasPagos->ticket_promedio ?? 0); ?></strong>
        <?php endif; ?>
      </p>
    </div>
    <div class="dashboard-grid cols-2" style="grid-template-columns: 2fr 1fr;">
      <div class="chart-container">
        <canvas id="ingresosChart"></canvas>
      </div>
      <div class="chart-container">
        <canvas id="estadosChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Últimas Transacciones -->
  <?php if (!empty($ultimosPagos)): ?>
  <div class="chart-section">
    <div class="chart-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
      <div>
        <h2 class="chart-title">
          <?php echo icon('list', 20); ?> Transacciones
        </h2>
        <p class="chart-subtitle">
          Monitorea las transacciones para detectar anomalías o patrones.
        </p>
      </div>
      <button onclick="exportarTransacciones()" class="btn outline" style="display: inline-flex; align-items: center; gap: 8px;">
        <?php echo icon('download', 18); ?>
        <span>Exportar CSV</span>
      </button>
    </div>
    
    <!-- Filtros -->
    <div style="margin-bottom: 24px; padding: 16px; background: var(--cc-bg-muted, #F9FAFB); border-radius: 12px; border: 1px solid var(--cc-border-default, #E5E7EB);">
      <form id="filtrosTransacciones" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
        <!-- Estado -->
        <div style="flex: 0 0 180px;">
          <label class="label" style="display: block; margin-bottom: 8px;">Estado</label>
          <select id="filtroEstado" class="input" onchange="filtrarTransacciones()">
            <option value="">Todos</option>
            <option value="aprobado">Aprobado</option>
            <option value="pendiente">Pendiente</option>
            <option value="rechazado">Rechazado</option>
          </select>
        </div>
        
        <!-- Tipo -->
        <div style="flex: 0 0 180px;">
          <label class="label" style="display: block; margin-bottom: 8px;">Tipo</label>
          <select id="filtroTipo" class="input" onchange="filtrarTransacciones()">
            <option value="">Todos</option>
            <option value="destacado_15">15 días</option>
            <option value="destacado_30">30 días</option>
          </select>
        </div>
        
        <!-- Búsqueda -->
        <div style="flex: 1; min-width: 250px;">
          <label class="label" style="display: block; margin-bottom: 8px;">Buscar</label>
          <input 
            type="text" 
            id="filtroBusqueda" 
            class="input" 
            placeholder="Usuario, email..." 
            oninput="filtrarTransacciones()"
          >
        </div>
        
        <!-- Botón limpiar -->
        <div>
          <button type="button" onclick="limpiarFiltros()" class="btn outline">
            <?php echo icon('x', 16); ?>
            Limpiar
          </button>
        </div>
      </form>
    </div>
    
    <!-- Vista de tabla para desktop -->
    <div class="transactions-table-view" style="overflow-x: auto;">
      <table class="data-table" id="tablaTransacciones">
        <thead>
          <tr>
            <th onclick="ordenarTabla('id')" style="cursor: pointer; user-select: none;">
              ID <?php echo icon('chevrons-up-down', 14); ?>
            </th>
            <th onclick="ordenarTabla('fecha')" style="cursor: pointer; user-select: none;">
              Fecha <?php echo icon('chevrons-up-down', 14); ?>
            </th>
            <th onclick="ordenarTabla('usuario')" style="cursor: pointer; user-select: none;">
              Usuario <?php echo icon('chevrons-up-down', 14); ?>
            </th>
            <th onclick="ordenarTabla('monto')" style="cursor: pointer; user-select: none;">
              Monto <?php echo icon('chevrons-up-down', 14); ?>
            </th>
            <th onclick="ordenarTabla('estado')" style="cursor: pointer; user-select: none;">
              Estado <?php echo icon('chevrons-up-down', 14); ?>
            </th>
            <th onclick="ordenarTabla('tipo')" style="cursor: pointer; user-select: none;">
              Tipo <?php echo icon('chevrons-up-down', 14); ?>
            </th>
          </tr>
        </thead>
        <tbody id="transaccionesBody">
          <?php foreach ($ultimosPagos as $pago): ?>
          <tr data-estado="<?php echo $pago->estado; ?>" data-tipo="<?php echo $pago->tipo; ?>" data-usuario="<?php echo strtolower($pago->usuario_nombre . ' ' . $pago->usuario_apellido . ' ' . $pago->usuario_email); ?>">
            <td><code style="background: var(--cc-bg-muted, #F3F4F6); padding: 4px 8px; border-radius: 4px; color: var(--cc-text-primary, #111827);">#<?php echo $pago->id; ?></code></td>
            <td data-sort="<?php echo strtotime($pago->fecha_creacion); ?>">
              <div style="font-weight: 600;"><?php echo date('d/m/Y', strtotime($pago->fecha_creacion)); ?></div>
              <small style="color: var(--cc-text-tertiary, #9CA3AF);"><?php echo date('H:i', strtotime($pago->fecha_creacion)); ?></small>
            </td>
            <td>
              <div style="font-weight: 500;"><?php echo htmlspecialchars($pago->usuario_nombre . ' ' . $pago->usuario_apellido); ?></div>
              <small style="color: var(--cc-text-tertiary, #9CA3AF);"><?php echo htmlspecialchars($pago->usuario_email); ?></small>
            </td>
            <td data-sort="<?php echo $pago->monto; ?>"><strong style="font-size: 15px;"><?php echo formatPrice($pago->monto); ?></strong></td>
            <td>
              <span class="badge badge-<?php
    echo $pago->estado === 'aprobado'
      ? 'success'
      : ($pago->estado === 'rechazado' ? 'danger' : 'warning');
    ?>">
                <?php echo ucfirst($pago->estado); ?>
              </span>
            </td>
            <td>
              <span class="badge badge-info">
                <?php
                $tipo = str_replace('destacado_', '', $pago->tipo);
                echo $tipo === '15' ? '15 días' : '30 días';
                ?>
              </span>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    
    <!-- Paginación -->
    <div id="paginacion" style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
      <div class="meta" id="infoPaginacion"></div>
      <div id="botonesPaginacion" style="display: flex; gap: 8px;"></div>
    </div>
    
    <!-- Vista de cards para móvil -->
    <div class="transactions-cards-view" style="display: none;">
      <?php foreach (array_slice($ultimosPagos, 0, 8) as $pago): ?>
      <div class="transaction-card">
        <div class="transaction-card-header">
          <code style="background: #F3F4F6; padding: 4px 8px; border-radius: 4px; font-size: 11px;">#<?php echo $pago->id; ?></code>
          <span class="badge badge-<?php
            echo $pago->estado === 'aprobado'
              ? 'success'
              : ($pago->estado === 'rechazado' ? 'danger' : 'warning');
          ?>" style="font-size: 11px;">
            <?php echo ucfirst($pago->estado); ?>
          </span>
        </div>
        <div class="transaction-card-body">
          <div class="transaction-card-row">
            <span class="transaction-label">Fecha:</span>
            <span class="transaction-value">
              <?php echo date('d/m/Y H:i', strtotime($pago->fecha_creacion)); ?>
            </span>
          </div>
          <div class="transaction-card-row">
            <span class="transaction-label">Usuario:</span>
            <span class="transaction-value">
              <?php echo htmlspecialchars($pago->usuario_nombre . ' ' . $pago->usuario_apellido); ?>
            </span>
          </div>
          <div class="transaction-card-row">
            <span class="transaction-label">Email:</span>
            <span class="transaction-value" style="font-size: 12px; color: #6B7280;">
              <?php echo htmlspecialchars($pago->usuario_email); ?>
            </span>
          </div>
          <div class="transaction-card-row">
            <span class="transaction-label">Monto:</span>
            <span class="transaction-value" style="font-weight: 700; color: #10B981; font-size: 16px;">
              <?php echo formatPrice($pago->monto); ?>
            </span>
          </div>
          <div class="transaction-card-row">
            <span class="transaction-label">Tipo:</span>
            <span class="badge badge-info" style="font-size: 11px;">
              <?php
              $tipo = str_replace('destacado_', '', $pago->tipo);
              echo $tipo === '15' ? '15 días' : '30 días';
              ?>
            </span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

</main>

<script>
// Configuración global optimizada
Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
Chart.defaults.responsive = true;
Chart.defaults.maintainAspectRatio = false;
Chart.defaults.plugins.legend.display = true;
Chart.defaults.plugins.legend.position = 'top';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.padding = 15;

// Configuración responsive para móvil
const isMobile = window.innerWidth <= 768;
if (isMobile) {
    Chart.defaults.plugins.legend.labels.padding = 8;
    Chart.defaults.plugins.legend.labels.font = { size: 11 };
    Chart.defaults.plugins.legend.labels.boxWidth = 12;
    Chart.defaults.plugins.legend.labels.boxHeight = 12;
    Chart.defaults.font.size = 10;
}

// Colores del design system
const colors = {
    primary: '#E6332A',
    success: '#10B981',
    warning: '#F59E0B',
    danger: '#EF4444',
    info: '#3B82F6',
    gray: '#6B7280'
};

// Datos
const usuariosData = <?php echo json_encode($usuariosPorMes ?? []); ?>;
const publicacionesData = <?php echo json_encode($publicacionesPorMes ?? []); ?>;
const ingresosData = <?php echo json_encode($ingresosMensuales ?? []); ?>;
const estadosData = <?php echo json_encode($pagosPorEstado ?? []); ?>;
const funnelData = <?php echo json_encode($funnelConversion ?? null); ?>;
const relacionData = <?php echo json_encode($relacionDestacadas ?? null); ?>;

// Formatear mes
function formatMes(mesStr) {
    const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    const [year, month] = mesStr.split('-');
    return meses[parseInt(month) - 1];
}

// Efecto 3D para gráficos
const shadow3D = {
    id: 'shadow3D',
    beforeDatasetsDraw(chart) {
        const { ctx } = chart;
        ctx.shadowColor = 'rgba(0, 0, 0, 0.15)';
        ctx.shadowBlur = 10;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 4;
    }
};

// Gráfico: Funnel de Conversión (Storytelling)
if (funnelData) {
    const totalUsuarios = funnelData.total_usuarios;
    const usuariosPublicaron = funnelData.usuarios_publicaron;
    const usuariosPagaron = funnelData.usuarios_pagaron;
    const usuariosConvirtieron = funnelData.usuarios_convirtieron;
    
    // Calcular porcentajes
    const pctPublicaron = ((usuariosPublicaron / totalUsuarios) * 100).toFixed(1);
    const pctPagaron = ((usuariosPagaron / totalUsuarios) * 100).toFixed(1);
    const pctConvirtieron = ((usuariosConvirtieron / totalUsuarios) * 100).toFixed(1);
    
    new Chart(document.getElementById('funnelChart'), {
        type: 'bar',
        data: {
            labels: [
                `1. Usuarios Registrados\n(${totalUsuarios})`,
                `2. Publicaron\n(${usuariosPublicaron} - ${pctPublicaron}%)`,
                `3. Intentaron Pagar\n(${usuariosPagaron} - ${pctPagaron}%)`,
                `4. Pagaron Exitosamente\n(${usuariosConvirtieron} - ${pctConvirtieron}%)`
            ],
            datasets: [{
                label: 'Usuarios en cada etapa',
                data: [totalUsuarios, usuariosPublicaron, usuariosPagaron, usuariosConvirtieron],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(16, 185, 129, 0.9)'
                ],
                borderColor: [
                    '#3B82F6',
                    '#10B981',
                    '#F59E0B',
                    '#10B981'
                ],
                borderWidth: 2,
                borderRadius: isMobile ? 6 : 8
            }]
        },
        plugins: [shadow3D],
        options: {
            indexAxis: 'y',
            layout: {
                padding: isMobile ? { top: 5, bottom: 10, left: 5, right: 5 } : { top: 10, bottom: 10 }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Funnel: De Usuario a Cliente',
                    font: { size: isMobile ? 13 : 16, weight: '700' },
                    padding: { bottom: isMobile ? 10 : 20 }
                },
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#111827',
                    padding: 12,
                    callbacks: {
                        label: (context) => {
                            const value = context.parsed.x;
                            const pct = ((value / totalUsuarios) * 100).toFixed(1);
                            return ` ${value} usuarios (${pct}%)`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { font: { size: isMobile ? 10 : 12 } },
                    grid: { color: '#F3F4F6' }
                },
                y: {
                    ticks: { 
                        font: { size: isMobile ? 11 : 13, weight: '600' },
                        autoSkip: false
                    },
                    grid: { display: false }
                }
            }
        }
    });
}

// Gráfico: Usuarios (Área con gradiente y 3D)
new Chart(document.getElementById('usuariosChart'), {
    type: 'line',
    data: {
        labels: usuariosData.map(d => formatMes(d.mes)),
        datasets: [{
            label: 'Nuevos Usuarios',
            data: usuariosData.map(d => d.cantidad),
            borderColor: '#3B82F6',
            backgroundColor: (context) => {
                const ctx = context.chart.ctx;
                const gradient = ctx.createLinearGradient(0, 0, 0, isMobile ? 200 : 300);
                gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
                gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');
                return gradient;
            },
            fill: true,
            tension: 0.4,
            borderWidth: isMobile ? 3 : 4,
            pointRadius: isMobile ? 4 : 6,
            pointHoverRadius: isMobile ? 7 : 9,
            pointBackgroundColor: '#fff',
            pointBorderWidth: isMobile ? 2 : 3,
            pointBorderColor: '#3B82F6',
            pointHoverBackgroundColor: '#3B82F6',
            pointHoverBorderColor: '#fff'
        }]
    },
    plugins: [shadow3D],
    options: {
        layout: {
            padding: isMobile ? { top: 5, bottom: 10, left: 5, right: 5 } : { top: 10, bottom: 10 }
        },
        plugins: {
            title: { 
                display: true, 
                text: 'Nuevos Usuarios por Mes', 
                font: { size: isMobile ? 13 : 16, weight: '700' },
                padding: { bottom: isMobile ? 10 : 20 }
            },
            legend: {
                display: true,
                labels: {
                    font: { size: isMobile ? 11 : 13, weight: '600' },
                    padding: isMobile ? 8 : 15,
                    boxWidth: isMobile ? 10 : 15,
                    boxHeight: isMobile ? 10 : 15
                }
            },
            tooltip: {
                backgroundColor: '#111827',
                padding: 12,
                titleFont: { size: 13 },
                bodyFont: { size: 14, weight: '600' },
                callbacks: {
                    label: (context) => ' ' + context.parsed.y + ' usuarios'
                }
            }
        },
        scales: {
            y: { 
                beginAtZero: true,
                ticks: { 
                    precision: 0, 
                    font: { size: isMobile ? 10 : 12 }
                },
                grid: { color: '#F3F4F6' }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: isMobile ? 10 : 12 } }
            }
        }
    }
});

// Gráfico: Publicaciones por Categoría (Stacked con 3D)
// Agrupar datos por mes y categoría
const mesesUnicos = [...new Set(publicacionesData.map(d => d.mes))];
const categoriasUnicas = [...new Set(publicacionesData.map(d => d.categoria || 'Sin categoría'))];

const coloresCategorias = [
    'rgba(59, 130, 246, 0.8)',
    'rgba(16, 185, 129, 0.8)',
    'rgba(245, 158, 11, 0.8)',
    'rgba(239, 68, 68, 0.8)',
    'rgba(139, 92, 246, 0.8)'
];

const datasets = categoriasUnicas.map((categoria, index) => {
    return {
        label: categoria,
        data: mesesUnicos.map(mes => {
            const item = publicacionesData.find(d => d.mes === mes && (d.categoria || 'Sin categoría') === categoria);
            return item ? item.cantidad : 0;
        }),
        backgroundColor: coloresCategorias[index % coloresCategorias.length],
        borderColor: coloresCategorias[index % coloresCategorias.length].replace('0.8', '1'),
        borderWidth: 2,
        borderRadius: 6
    };
});

new Chart(document.getElementById('publicacionesChart'), {
    type: 'bar',
    data: {
        labels: mesesUnicos.map(m => formatMes(m)),
        datasets: datasets
    },
    plugins: [shadow3D],
    options: {
        layout: {
            padding: isMobile ? { top: 5, bottom: 10, left: 5, right: 5 } : { top: 10, bottom: 10 }
        },
        plugins: {
            title: { 
                display: true, 
                text: 'Publicaciones por Categoría de Vehículo', 
                font: { size: isMobile ? 13 : 16, weight: '700' },
                padding: { bottom: isMobile ? 10 : 20 }
            },
            legend: {
                display: true,
                position: 'top',
                labels: {
                    font: { size: isMobile ? 10 : 12, weight: '600' },
                    padding: isMobile ? 8 : 12,
                    usePointStyle: true,
                    boxWidth: isMobile ? 10 : 15,
                    boxHeight: isMobile ? 10 : 15
                }
            },
            tooltip: {
                backgroundColor: '#111827',
                padding: 12,
                callbacks: {
                    label: (context) => ` ${context.dataset.label}: ${context.parsed.y}`
                }
            }
        },
        scales: {
            y: { 
                stacked: true,
                beginAtZero: true,
                ticks: { 
                    precision: 0,
                    font: { size: isMobile ? 10 : 12 }
                },
                grid: { color: '#F3F4F6' }
            },
            x: { 
                stacked: true,
                ticks: { font: { size: isMobile ? 10 : 12 } },
                grid: { display: false } 
            }
        }
    }
});

// Gráfico: Ingresos con relación a pagos (Barras con 3D)
new Chart(document.getElementById('ingresosChart'), {
    type: 'bar',
    data: {
        labels: ingresosData.map(d => formatMes(d.mes)),
        datasets: [{
            label: 'Ingresos Totales',
            data: ingresosData.map(d => d.total_ingresos),
            backgroundColor: 'rgba(16, 185, 129, 0.8)',
            borderColor: '#10B981',
            borderWidth: 2,
            borderRadius: isMobile ? 6 : 8,
            hoverBackgroundColor: '#059669'
        }]
    },
    plugins: [shadow3D],
    options: {
        plugins: {
            title: { 
                display: true, 
                text: 'Ingresos Mensuales', 
                font: { size: isMobile ? 13 : 16, weight: '700' },
                padding: { bottom: isMobile ? 10 : 20 }
            },
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: '#111827',
                padding: 12,
                callbacks: {
                    label: (context) => {
                        if (context.datasetIndex === 0) {
                            return ` Ingresos: $${context.parsed.y.toLocaleString('es-CL')}`;
                        } else {
                            const cantidad = Math.round(context.parsed.y / 5000);
                            return ` Pagos: ${cantidad} transacciones`;
                        }
                    }
                }
            }
        },
        layout: {
            padding: isMobile ? { top: 5, bottom: 10, left: 5, right: 5 } : { top: 10, bottom: 10 }
        },
        scales: {
            y: { 
                type: 'linear',
                position: 'left',
                beginAtZero: true,
                ticks: {
                    font: { size: isMobile ? 10 : 12 },
                    callback: (value) => '$' + (value / 1000).toFixed(0) + 'k'
                },
                grid: { color: '#F3F4F6' }
            },
            x: { 
                ticks: { font: { size: isMobile ? 10 : 12 } },
                grid: { display: false } 
            }
        }
    }
});

// Gráfico: Estados (Dona mejorada)
if (estadosData.length > 0) {
    const colores = {
        'aprobado': '#10B981',
        'rechazado': '#EF4444',
        'pendiente': '#F59E0B',
        'expirado': '#6B7280'
    };
    
    new Chart(document.getElementById('estadosChart'), {
        type: 'doughnut',
        data: {
            labels: estadosData.map(d => d.estado.charAt(0).toUpperCase() + d.estado.slice(1)),
            datasets: [{
                data: estadosData.map(d => d.cantidad),
                backgroundColor: estadosData.map(d => colores[d.estado] || '#6B7280'),
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                title: { 
                    display: false
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 13,
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: '#111827',
                    padding: 12,
                    callbacks: {
                        label: (context) => {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// ============================================================================
// GRÁFICOS DE ADMIN (Actividad, Estados, Categorías)
// ============================================================================

// Preparar datos de actividad semanal (últimos 7 días)
<?php
$fechas = [];
$totales = [];
for ($i = 6; $i >= 0; $i--) {
  $fecha = date('Y-m-d', strtotime("-$i days"));
  $fechas[] = date('d/m', strtotime($fecha));

  // Buscar si hay datos para esta fecha
  $total = 0;
  foreach ($chartData['actividad_semanal'] as $dia) {
    if ($dia->fecha === $fecha) {
      $total = $dia->total;
      break;
    }
  }
  $totales[] = $total;
}
?>

// Gráfico: Actividad Semanal
const ctxActividad = document.getElementById('chartActividadSemanal');
if (ctxActividad) {
    new Chart(ctxActividad, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($fechas); ?>,
            datasets: [{
                label: 'Publicaciones',
                data: <?php echo json_encode($totales); ?>,
                borderColor: '#E6332A',
                backgroundColor: 'rgba(230, 51, 42, 0.1)',
                borderWidth: isMobile ? 2 : 3,
                fill: true,
                tension: 0.4,
                pointRadius: isMobile ? 4 : 5,
                pointHoverRadius: isMobile ? 6 : 7,
                pointBackgroundColor: '#E6332A',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: isMobile ? { top: 5, bottom: 10, left: 5, right: 5 } : { top: 10, bottom: 10 }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1A1A1A',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: { size: 13, weight: '600' },
                    bodyFont: { size: 14, weight: '700' },
                    callbacks: {
                        label: (context) => context.parsed.y + ' publicaciones'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1, 
                        font: { size: isMobile ? 10 : 12 } 
                    },
                    grid: { color: '#F3F4F6' }
                },
                x: {
                    ticks: { font: { size: isMobile ? 10 : 12 } },
                    grid: { display: false }
                }
            }
        }
    });
}

// Gráfico: Distribución de Estados (Dona)
const ctxEstados = document.getElementById('chartDistribucionEstados');
if (ctxEstados) {
    new Chart(ctxEstados, {
        type: 'doughnut',
        data: {
            labels: ['Aprobadas', 'Pendientes', 'Rechazadas', 'Borradores', 'Vendidas', 'Archivadas'],
            datasets: [{
                data: [
                    <?php echo $chartData['distribucion_estados']['aprobadas'] ?? 0; ?>,
                    <?php echo $chartData['distribucion_estados']['pendientes'] ?? 0; ?>,
                    <?php echo $chartData['distribucion_estados']['rechazadas'] ?? 0; ?>,
                    <?php echo $chartData['distribucion_estados']['borradores'] ?? 0; ?>,
                    <?php echo $chartData['distribucion_estados']['vendidas'] ?? 0; ?>,
                    <?php echo $chartData['distribucion_estados']['archivadas'] ?? 0; ?>
                ],
                backgroundColor: [
                    colors.success,    // Aprobadas - Verde
                    colors.warning,    // Pendientes - Amarillo
                    colors.danger,     // Rechazadas - Rojo
                    colors.gray,       // Borradores - Gris
                    colors.info,       // Vendidas - Azul
                    '#6B7280'          // Archivadas - Gris oscuro
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: isMobile ? { top: 5, bottom: 10, left: 5, right: 5 } : { top: 10, bottom: 10 }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 13,
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: '#1A1A1A',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: (context) => {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// Gráfico: Top 5 Categorías (barras verticales con colores variados)
const ctxCategorias = document.getElementById('chartPorCategoria');
if (ctxCategorias) {
    new Chart(ctxCategorias, {
        type: 'bar',
        data: {
            labels: [
                <?php foreach ($chartData['por_categoria'] as $cat): ?>
                    '<?php echo addslashes($cat->categoria); ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                label: 'Publicaciones',
                data: [
                    <?php foreach ($chartData['por_categoria'] as $cat): ?>
                        <?php echo $cat->total; ?>,
                    <?php endforeach; ?>
                ],
                backgroundColor: [
                    colors.primary,   // Rojo
                    colors.info,      // Azul
                    colors.success,   // Verde
                    colors.warning,   // Amarillo
                    colors.gray       // Gris
                ],
                borderColor: [
                    colors.primary,
                    colors.info,
                    colors.success,
                    colors.warning,
                    colors.gray
                ],
                borderWidth: 2,
                borderRadius: isMobile ? 6 : 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: isMobile ? { top: 5, bottom: 10, left: 5, right: 5 } : { top: 10, bottom: 10 }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1A1A1A',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: (context) => context.parsed.y + ' publicaciones'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1, 
                        font: { size: isMobile ? 10 : 12 } 
                    },
                    grid: { color: '#F3F4F6' }
                },
                x: {
                    ticks: { font: { size: isMobile ? 10 : 12 } },
                    grid: { display: false }
                }
            }
        }
    });
}

// Gráfico: Top 5 Subcategorías (barras horizontales)
const ctxSubcategorias = document.getElementById('chartPorSubcategoria');
if (ctxSubcategorias) {
    new Chart(ctxSubcategorias, {
        type: 'bar',
        data: {
            labels: [
                <?php foreach ($chartData['por_subcategoria'] as $subcat): ?>
                    '<?php echo addslashes($subcat->categoria); ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                label: 'Publicaciones',
                data: [
                    <?php foreach ($chartData['por_subcategoria'] as $subcat): ?>
                        <?php echo $subcat->total; ?>,
                    <?php endforeach; ?>
                ],
                backgroundColor: [
                    colors.primary,
                    colors.info,
                    colors.success,
                    colors.warning,
                    colors.gray
                ],
                borderRadius: isMobile ? 6 : 8,
                barThickness: 40
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: isMobile ? { top: 5, bottom: 10, left: 5, right: 5 } : { top: 10, bottom: 10 }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1A1A1A',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: { size: 13, weight: '600' },
                    bodyFont: { size: 14, weight: '700' },
                    callbacks: {
                        label: (context) => context.parsed.x + ' publicaciones'
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1, 
                        font: { size: isMobile ? 10 : 12 } 
                    },
                    grid: { color: '#F3F4F6' }
                },
                y: {
                    ticks: { 
                        font: { size: isMobile ? 11 : 13, weight: '600' } 
                    },
                    grid: { display: false }
                }
            }
        }
    });
}

// ============================================================================
// SISTEMA DE FILTROS, ORDENAMIENTO Y PAGINACIÓN DE TRANSACCIONES
// ============================================================================

let transaccionesOriginales = [];
let transaccionesFiltradas = [];
let ordenActual = { columna: null, direccion: 'asc' };
let paginaActual = 1;
const itemsPorPagina = 10;

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    cargarTransacciones();
    actualizarTabla();
});

function cargarTransacciones() {
    const filas = document.querySelectorAll('#transaccionesBody tr');
    transaccionesOriginales = Array.from(filas).map(fila => ({
        elemento: fila,
        id: parseInt(fila.querySelector('code').textContent.replace('#', '')),
        fecha: parseInt(fila.querySelector('td[data-sort]').getAttribute('data-sort')),
        usuario: fila.getAttribute('data-usuario'),
        monto: parseFloat(fila.querySelectorAll('td')[3].getAttribute('data-sort')),
        estado: fila.getAttribute('data-estado'),
        tipo: fila.getAttribute('data-tipo')
    }));
    transaccionesFiltradas = [...transaccionesOriginales];
}

function filtrarTransacciones() {
    const estado = document.getElementById('filtroEstado').value;
    const tipo = document.getElementById('filtroTipo').value;
    const busqueda = document.getElementById('filtroBusqueda').value.toLowerCase();
    
    transaccionesFiltradas = transaccionesOriginales.filter(t => {
        const cumpleEstado = !estado || t.estado === estado;
        const cumpleTipo = !tipo || t.tipo === tipo;
        const cumpleBusqueda = !busqueda || t.usuario.includes(busqueda);
        
        return cumpleEstado && cumpleTipo && cumpleBusqueda;
    });
    
    paginaActual = 1;
    actualizarTabla();
}

function limpiarFiltros() {
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroTipo').value = '';
    document.getElementById('filtroBusqueda').value = '';
    filtrarTransacciones();
}

function ordenarTabla(columna) {
    if (ordenActual.columna === columna) {
        ordenActual.direccion = ordenActual.direccion === 'asc' ? 'desc' : 'asc';
    } else {
        ordenActual.columna = columna;
        ordenActual.direccion = 'asc';
    }
    
    transaccionesFiltradas.sort((a, b) => {
        let valorA = a[columna];
        let valorB = b[columna];
        
        if (typeof valorA === 'string') {
            valorA = valorA.toLowerCase();
            valorB = valorB.toLowerCase();
        }
        
        if (ordenActual.direccion === 'asc') {
            return valorA > valorB ? 1 : -1;
        } else {
            return valorA < valorB ? 1 : -1;
        }
    });
    
    actualizarTabla();
}

function actualizarTabla() {
    const tbody = document.getElementById('transaccionesBody');
    tbody.innerHTML = '';
    
    const inicio = (paginaActual - 1) * itemsPorPagina;
    const fin = inicio + itemsPorPagina;
    const transaccionesPagina = transaccionesFiltradas.slice(inicio, fin);
    
    transaccionesPagina.forEach(t => {
        tbody.appendChild(t.elemento.cloneNode(true));
    });
    
    actualizarPaginacion();
}

function actualizarPaginacion() {
    const totalPaginas = Math.ceil(transaccionesFiltradas.length / itemsPorPagina);
    const inicio = (paginaActual - 1) * itemsPorPagina + 1;
    const fin = Math.min(paginaActual * itemsPorPagina, transaccionesFiltradas.length);
    
    // Actualizar info
    document.getElementById('infoPaginacion').textContent = 
        `Mostrando ${inicio}-${fin} de ${transaccionesFiltradas.length} transacciones`;
    
    // Actualizar botones
    const botones = document.getElementById('botonesPaginacion');
    botones.innerHTML = '';
    
    if (totalPaginas <= 1) return;
    
    // Botón anterior
    if (paginaActual > 1) {
        const btnAnterior = document.createElement('button');
        btnAnterior.className = 'btn outline btn-sm';
        btnAnterior.innerHTML = '← Anterior';
        btnAnterior.onclick = () => cambiarPagina(paginaActual - 1);
        botones.appendChild(btnAnterior);
    }
    
    // Botones de páginas
    const rango = 2;
    for (let i = Math.max(1, paginaActual - rango); i <= Math.min(totalPaginas, paginaActual + rango); i++) {
        const btnPagina = document.createElement('button');
        btnPagina.className = i === paginaActual ? 'btn primary btn-sm' : 'btn outline btn-sm';
        btnPagina.textContent = i;
        btnPagina.onclick = () => cambiarPagina(i);
        botones.appendChild(btnPagina);
    }
    
    // Botón siguiente
    if (paginaActual < totalPaginas) {
        const btnSiguiente = document.createElement('button');
        btnSiguiente.className = 'btn outline btn-sm';
        btnSiguiente.innerHTML = 'Siguiente →';
        btnSiguiente.onclick = () => cambiarPagina(paginaActual + 1);
        botones.appendChild(btnSiguiente);
    }
}

function cambiarPagina(pagina) {
    paginaActual = pagina;
    actualizarTabla();
    document.querySelector('.transactions-table-view').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function exportarTransacciones() {
    // Preparar datos
    const datos = transaccionesFiltradas.map(t => {
        const celdas = t.elemento.querySelectorAll('td');
        return {
            ID: t.id,
            Fecha: celdas[1].querySelector('div').textContent + ' ' + celdas[1].querySelector('small').textContent,
            Usuario: celdas[2].querySelector('div').textContent,
            Email: celdas[2].querySelector('small').textContent,
            Monto: celdas[3].textContent.trim(),
            Estado: celdas[4].textContent.trim(),
            Tipo: celdas[5].textContent.trim()
        };
    });
    
    // Crear CSV
    const headers = Object.keys(datos[0]);
    let csv = headers.join(',') + '\n';
    
    datos.forEach(fila => {
        csv += headers.map(h => {
            const valor = fila[h].toString();
            return valor.includes(',') ? `"${valor}"` : valor;
        }).join(',') + '\n';
    });
    
    // Descargar
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `transacciones_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

</script>

<!-- Script de tema (modo claro/oscuro) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Theme toggle
    const themeToggle = document.querySelector('.theme-toggle');
    const html = document.documentElement;
    
    // Cargar tema guardado
    const savedTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', savedTheme);
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
    
    // Inicializar iconos de Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>

</main>
</body>
</html>
