<?php
/**
 * Vista: Dashboard Principal - Panel Admin
 * Centro de control ejecutivo con métricas clave y alertas
 * 
 * @author ChileChocados
 * @date 2025-10-30
 * REDISEÑADO: Eliminada duplicidad, enfoque en acciones importantes
 */

// La verificación de admin se hace en el controlador
layout('header');
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin-layout.css">

<main class="container admin-container">

  <!-- Encabezado con bienvenida -->
  <div style="margin-bottom: 32px;">
    <h1 class="h1" style="margin-bottom: 8px; display: flex; align-items: center; gap: 12px;">
      <?php echo icon('user-circle', 32); ?>
      Bienvenido, <?php echo htmlspecialchars($adminNombre); ?>
    </h1>
    <p class="meta" style="font-size: 15px; display: flex; align-items: center; gap: 8px;">
      <?php echo icon('calendar', 16); ?>
      <?php echo formatDateSpanish(time(), 'full'); ?> • 
      <?php echo icon('clock', 16); ?>
      <?php echo date('H:i'); ?>
    </p>
  </div>

  <!-- Alertas y Acciones Pendientes -->
  <?php if ($metricas['publicaciones_pendientes'] > 0): ?>
    <div class="alert-card alert-warning" style="margin-bottom: 32px;">
      <div class="alert-icon"><?php echo icon('alert-triangle', 48); ?></div>
      <div class="alert-content">
        <h3 class="alert-title">
          <?php echo $metricas['publicaciones_pendientes']; ?> 
          <?php echo $metricas['publicaciones_pendientes'] === 1 ? 'publicación pendiente' : 'publicaciones pendientes'; ?>
        </h3>
        <p class="alert-description">
          Hay publicaciones esperando tu aprobación para ser publicadas
        </p>
        <div class="alert-meta">
          <?php echo icon('clock', 12); ?> Última actualización: hace unos momentos
        </div>
      </div>
      <div class="alert-action">
        <a href="<?php echo BASE_URL; ?>/admin/publicaciones?estado=pendiente" 
           class="btn btn-primary btn-lg">
          <?php echo icon('arrow-right', 18); ?> Ir a Moderar
        </a>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($metricas['mensajes_sin_leer'] > 0): ?>
    <div class="alert-card alert-info" style="margin-bottom: 32px;">
      <div class="alert-icon"><?php echo icon('message-circle', 48); ?></div>
      <div class="alert-content">
        <h3 class="alert-title">
          <?php echo $metricas['mensajes_sin_leer']; ?> 
          <?php echo $metricas['mensajes_sin_leer'] === 1 ? 'mensaje sin leer' : 'mensajes sin leer'; ?>
        </h3>
        <p class="alert-description">
          Tienes conversaciones pendientes de revisar
        </p>
      </div>
      <div class="alert-action">
        <a href="<?php echo BASE_URL; ?>/admin/mensajes" 
           class="btn btn-primary btn-lg">
          <?php echo icon('arrow-right', 18); ?> Ver Mensajes
        </a>
      </div>
    </div>
  <?php endif; ?>

  <!-- Métricas Clave -->
  <div style="margin-bottom: 32px;">
    <h2 class="h2" style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
      <?php echo icon('trending-up', 24); ?> Métricas Clave
    </h2>
    
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
      <!-- Publicaciones Activas -->
      <a href="<?php echo BASE_URL; ?>/admin/publicaciones?estado=aprobada" 
         class="metric-card" style="text-decoration: none;">
        <div class="metric-icon"><?php echo icon('file', 36); ?></div>
        <div class="metric-content">
          <div class="metric-label">Publicaciones Activas</div>
          <div class="metric-value"><?php echo number_format($metricas['publicaciones_activas']); ?></div>
          <div class="metric-description">Visibles en el sitio</div>
        </div>
      </a>

      <!-- Usuarios -->
      <a href="<?php echo BASE_URL; ?>/admin/usuarios" 
         class="metric-card" style="text-decoration: none;">
        <div class="metric-icon"><?php echo icon('users', 36); ?></div>
        <div class="metric-content">
          <div class="metric-label">Usuarios Registrados</div>
          <div class="metric-value"><?php echo number_format($metricas['usuarios_total']); ?></div>
          <div class="metric-description">
            <?php if ($metricas['usuarios_nuevos_semana'] > 0): ?>
              <span class="metric-trend positive">
                <?php echo icon('trending-up', 14); ?> +<?php echo $metricas['usuarios_nuevos_semana']; ?> esta semana
              </span>
            <?php else: ?>
              <?php echo number_format($metricas['usuarios_activos']); ?> activos
            <?php endif; ?>
          </div>
        </div>
      </a>

      <!-- Destacadas -->
      <a href="<?php echo BASE_URL; ?>/admin/publicaciones" 
         class="metric-card" style="text-decoration: none;">
        <div class="metric-icon"><?php echo icon('star', 36); ?></div>
        <div class="metric-content">
          <div class="metric-label">Publicaciones Destacadas</div>
          <div class="metric-value"><?php echo number_format($metricas['destacadas_activas']); ?></div>
          <div class="metric-description">Activas actualmente</div>
        </div>
      </a>

      <!-- Pendientes -->
      <a href="<?php echo BASE_URL; ?>/admin/publicaciones?estado=pendiente" 
         class="metric-card metric-card-warning" style="text-decoration: none;">
        <div class="metric-icon"><?php echo icon('clock', 36); ?></div>
        <div class="metric-content">
          <div class="metric-label">Pendientes de Aprobar</div>
          <div class="metric-value"><?php echo number_format($metricas['publicaciones_pendientes']); ?></div>
          <div class="metric-description">
            <?php if ($metricas['publicaciones_pendientes'] > 0): ?>
              <span class="metric-trend warning">
                <?php echo icon('alert-circle', 14); ?> Requieren atención
              </span>
            <?php else: ?>
              <?php echo icon('check-circle', 14); ?> Todo al día
            <?php endif; ?>
          </div>
        </div>
      </a>
      
      <!-- Visitas Totales -->
      <a href="<?php echo BASE_URL; ?>/admin/reportes" 
         class="metric-card" style="text-decoration: none;">
        <div class="metric-icon"><?php echo icon('eye', 36); ?></div>
        <div class="metric-content">
          <div class="metric-label">Visitas Totales</div>
          <div class="metric-value"><?php echo number_format($metricas['visitas_totales']); ?></div>
          <div class="metric-description">En publicaciones activas</div>
        </div>
      </a>
    </div>
  </div>

  <!-- Gráficos de Actividad -->
  <div style="margin-bottom: 32px;">
    <h2 class="h2" style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
      <?php echo icon('trending-up', 24); ?> Actividad y Estadísticas
    </h2>
    
    <div class="grid cols-2" style="gap: 16px;">
      <!-- Gráfico de Actividad Semanal -->
      <div class="card" style="padding: 24px;">
        <h3 class="h3" style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
          <?php echo icon('activity', 20); ?>
          Publicaciones (Últimos 7 días)
        </h3>
        <canvas id="chartActividadSemanal" style="max-height: 250px;"></canvas>
      </div>
      
      <!-- Gráfico de Distribución de Estados -->
      <div class="card" style="padding: 24px;">
        <h3 class="h3" style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
          <?php echo icon('pie-chart', 20); ?>
          Distribución por Estado
        </h3>
        <canvas id="chartDistribucionEstados" style="max-height: 250px;"></canvas>
      </div>
    </div>
  </div>
  
  <!-- Gráfico de Categorías -->
  <div style="margin-bottom: 32px;">
    <div class="card" style="padding: 24px;">
      <h3 class="h3" style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
        <?php echo icon('bar-chart-2', 20); ?>
        Top 5 Categorías Más Populares
      </h3>
      <canvas id="chartPorCategoria" style="max-height: 300px;"></canvas>
    </div>
  </div>

  <!-- Accesos Rápidos -->
  <div style="margin-bottom: 32px;">
    <h2 class="h2" style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
      <?php echo icon('grid', 24); ?> Accesos Rápidos
    </h2>
    
    <div class="grid cols-3" style="gap: 16px;">
      <a href="<?php echo BASE_URL; ?>/admin/publicaciones" class="quick-access-card">
        <div class="quick-access-icon"><?php echo icon('file-text', 40); ?></div>
        <div class="quick-access-content">
          <div class="quick-access-title">Gestionar Publicaciones</div>
          <div class="quick-access-description">Moderar, aprobar y rechazar</div>
        </div>
        <?php echo icon('chevron-right', 20); ?>
      </a>

      <a href="<?php echo BASE_URL; ?>/admin/usuarios" class="quick-access-card">
        <div class="quick-access-icon"><?php echo icon('users', 40); ?></div>
        <div class="quick-access-content">
          <div class="quick-access-title">Gestionar Usuarios</div>
          <div class="quick-access-description">Ver, editar y administrar</div>
        </div>
        <?php echo icon('chevron-right', 20); ?>
      </a>

      <a href="<?php echo BASE_URL; ?>/admin/mensajes" class="quick-access-card">
        <div class="quick-access-icon"><?php echo icon('message-square', 40); ?></div>
        <div class="quick-access-content">
          <div class="quick-access-title">Centro de Mensajería</div>
          <div class="quick-access-description">Ver todas las conversaciones</div>
          <?php if ($metricas['mensajes_sin_leer'] > 0): ?>
            <span class="quick-access-badge"><?php echo $metricas['mensajes_sin_leer']; ?></span>
          <?php endif; ?>
        </div>
        <?php echo icon('chevron-right', 20); ?>
      </a>
    </div>
  </div>

  <!-- Estadísticas Adicionales -->
  <div style="margin-bottom: 32px;">
    <h2 class="h2" style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
      <?php echo icon('pie-chart', 24); ?> Resumen del Sistema
    </h2>
    
    <div class="card" style="padding: 24px;">
      <div class="grid cols-3" style="gap: 24px;">
        <div style="text-align: center; padding: 16px;">
          <?php echo icon('check-circle', 32); ?>
          <div class="h2" style="color: var(--cc-success, #10B981); margin: 8px 0 4px;">
            <?php echo number_format($metricas['publicaciones_activas']); ?>
          </div>
          <div class="meta">Publicaciones Aprobadas</div>
        </div>

        <div style="text-align: center; padding: 16px;">
          <?php echo icon('x-circle', 32); ?>
          <div class="h2" style="color: var(--cc-danger, #EF4444); margin: 8px 0 4px;">
            <?php echo number_format($metricas['publicaciones_rechazadas']); ?>
          </div>
          <div class="meta">Publicaciones Rechazadas</div>
        </div>

        <div style="text-align: center; padding: 16px;">
          <?php echo icon('user-check', 32); ?>
          <div class="h2" style="color: var(--cc-info, #3B82F6); margin: 8px 0 4px;">
            <?php echo number_format($metricas['usuarios_activos']); ?>
          </div>
          <div class="meta">Usuarios Activos</div>
        </div>
      </div>
    </div>
  </div>

</main>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Estilos específicos del dashboard usando design system -->
<style>
/* Alert Cards - Usando variables del design system */
.alert-card {
  display: flex;
  align-items: center;
  gap: var(--cc-space-6, 24px);
  padding: var(--cc-space-6, 24px);
  border-radius: var(--cc-radius-lg, 12px);
  border: 2px solid;
  transition: var(--cc-transition, all 0.2s ease);
}

.alert-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--cc-shadow-md, 0 8px 16px rgba(0, 0, 0, 0.1));
}

.alert-card.alert-warning {
  background: var(--cc-warning-light, #FFF3CD);
  border-color: var(--cc-warning, #F59E0B);
}

.alert-card.alert-info {
  background: var(--cc-info-light, #DBEAFE);
  border-color: var(--cc-info, #3B82F6);
}

.alert-icon {
  font-size: 48px;
  flex-shrink: 0;
  line-height: 1;
}

.alert-content {
  flex: 1;
}

.alert-title {
  font-size: var(--cc-text-xl, 20px);
  font-weight: var(--cc-font-bold, 700);
  margin-bottom: 4px;
}

.alert-card.alert-warning .alert-title {
  color: var(--cc-warning-dark, #D97706);
}

.alert-card.alert-info .alert-title {
  color: var(--cc-info-dark, #2563EB);
}

.alert-description {
  font-size: var(--cc-text-base, 15px);
  margin-bottom: 8px;
  color: var(--cc-text-secondary, #4A4A4A);
}

.alert-meta {
  font-size: var(--cc-text-xs, 12px);
  color: var(--cc-text-tertiary, #666666);
}

.alert-action {
  flex-shrink: 0;
}

/* Metric Cards - Usando design system */
.metric-card {
  background: var(--cc-bg-surface, white);
  border: 2px solid var(--cc-border-default, #D4D4D4);
  border-radius: var(--cc-radius-lg, 12px);
  padding: var(--cc-space-6, 24px);
  display: flex;
  flex-direction: column;
  gap: var(--cc-space-3, 12px);
  transition: var(--cc-transition, all 0.2s ease);
  cursor: pointer;
}

.metric-card:hover {
  border-color: var(--cc-primary, #E6332A);
  box-shadow: var(--cc-shadow-md, 0 4px 12px rgba(230, 51, 42, 0.1));
  transform: translateY(-2px);
}

.metric-card.metric-card-warning {
  background: var(--cc-warning-light, #FFF3CD);
  border-color: var(--cc-warning, #F59E0B);
}

.metric-card.metric-card-warning:hover {
  border-color: var(--cc-warning-dark, #D97706);
  box-shadow: 0 4px 12px rgba(217, 119, 6, 0.15);
}

.metric-icon {
  font-size: 36px;
  line-height: 1;
}

.metric-content {
  flex: 1;
}

.metric-label {
  font-size: var(--cc-text-sm, 13px);
  color: var(--cc-text-tertiary, #666666);
  font-weight: var(--cc-font-semibold, 600);
  margin-bottom: 8px;
}

.metric-value {
  font-size: 36px;
  font-weight: var(--cc-font-bold, 700);
  color: var(--cc-text-primary, #1A1A1A);
  line-height: 1;
  margin-bottom: 4px;
}

.metric-description {
  font-size: var(--cc-text-sm, 13px);
  color: var(--cc-text-tertiary, #666666);
}

.metric-trend {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: var(--cc-text-sm, 13px);
  font-weight: var(--cc-font-semibold, 600);
}

.metric-trend.positive {
  color: var(--cc-success, #10B981);
}

.metric-trend.warning {
  color: var(--cc-warning-dark, #D97706);
}

/* Quick Access Cards */
.quick-access-card {
  background: var(--cc-bg-surface, white);
  border: 2px solid var(--cc-border-default, #D4D4D4);
  border-radius: var(--cc-radius-lg, 12px);
  padding: var(--cc-space-6, 24px);
  display: flex;
  align-items: center;
  gap: var(--cc-space-4, 16px);
  text-decoration: none;
  color: inherit;
  transition: var(--cc-transition, all 0.2s ease);
  position: relative;
}

.quick-access-card:hover {
  border-color: var(--cc-primary, #E6332A);
  box-shadow: var(--cc-shadow-md, 0 4px 12px rgba(230, 51, 42, 0.1));
  transform: translateY(-2px);
}

.quick-access-icon {
  font-size: 40px;
  flex-shrink: 0;
  line-height: 1;
}

.quick-access-content {
  flex: 1;
}

.quick-access-title {
  font-size: var(--cc-text-lg, 16px);
  font-weight: var(--cc-font-bold, 700);
  color: var(--cc-text-primary, #1A1A1A);
  margin-bottom: 4px;
}

.quick-access-description {
  font-size: var(--cc-text-sm, 13px);
  color: var(--cc-text-tertiary, #666666);
}

.quick-access-badge {
  position: absolute;
  top: 16px;
  right: 16px;
  background: var(--cc-primary, #E6332A);
  color: var(--cc-white, white);
  font-size: var(--cc-text-xs, 12px);
  font-weight: var(--cc-font-bold, 700);
  padding: 4px 10px;
  border-radius: 12px;
  min-width: 24px;
  text-align: center;
}

/* Responsive */
@media (max-width: 968px) {
  .grid.cols-4 {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .grid.cols-3 {
    grid-template-columns: 1fr;
  }
  
  .alert-card {
    flex-direction: column;
    text-align: center;
  }
  
  .alert-action {
    width: 100%;
  }
  
  .alert-action .btn {
    width: 100%;
  }
}

@media (max-width: 640px) {
  .grid.cols-4 {
    grid-template-columns: 1fr;
  }
}
</style>

<script>
// Configuración global de Chart.js
Chart.defaults.font.family = 'Inter, system-ui, -apple-system, sans-serif';
Chart.defaults.color = '#666666';
Chart.defaults.borderColor = '#E5E7EB';

// Colores del design system
const colors = {
  primary: '#E6332A',
  success: '#10B981',
  warning: '#F59E0B',
  danger: '#EF4444',
  info: '#3B82F6',
  gray: '#6B7280'
};

// ============================================================================
// GRÁFICO 1: Actividad Semanal (Líneas)
// ============================================================================
<?php
// Preparar datos para JavaScript
$fechas = [];
$totales = [];

// Llenar con los últimos 7 días (incluso si no hay datos)
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

const ctxActividad = document.getElementById('chartActividadSemanal');
if (ctxActividad) {
  new Chart(ctxActividad, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($fechas); ?>,
      datasets: [{
        label: 'Publicaciones',
        data: <?php echo json_encode($totales); ?>,
        borderColor: colors.primary,
        backgroundColor: colors.primary + '20',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointRadius: 4,
        pointHoverRadius: 6,
        pointBackgroundColor: colors.primary,
        pointBorderColor: '#fff',
        pointBorderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: '#1A1A1A',
          padding: 12,
          cornerRadius: 8,
          titleFont: {
            size: 13,
            weight: '600'
          },
          bodyFont: {
            size: 14,
            weight: '700'
          },
          callbacks: {
            label: function(context) {
              return context.parsed.y + ' publicaciones';
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
            font: {
              size: 12
            }
          },
          grid: {
            color: '#F3F4F6'
          }
        },
        x: {
          ticks: {
            font: {
              size: 12
            }
          },
          grid: {
            display: false
          }
        }
      }
    }
  });
}

// ============================================================================
// GRÁFICO 2: Distribución de Estados (Dona)
// ============================================================================
const ctxEstados = document.getElementById('chartDistribucionEstados');
if (ctxEstados) {
  new Chart(ctxEstados, {
    type: 'doughnut',
    data: {
      labels: ['Aprobadas', 'Pendientes', 'Rechazadas'],
      datasets: [{
        data: [
          <?php echo $chartData['distribucion_estados']['aprobadas']; ?>,
          <?php echo $chartData['distribucion_estados']['pendientes']; ?>,
          <?php echo $chartData['distribucion_estados']['rechazadas']; ?>
        ],
        backgroundColor: [
          colors.success,
          colors.warning,
          colors.danger
        ],
        borderWidth: 0,
        hoverOffset: 10
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
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
          titleFont: {
            size: 13,
            weight: '600'
          },
          bodyFont: {
            size: 14,
            weight: '700'
          },
          callbacks: {
            label: function(context) {
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
// GRÁFICO 3: Publicaciones por Categoría (Barras Horizontales)
// ============================================================================
<?php
$categorias = [];
$totalesCat = [];
foreach ($chartData['por_categoria'] as $cat) {
    $categorias[] = $cat->categoria;
    $totalesCat[] = $cat->total;
}
?>

const ctxCategorias = document.getElementById('chartPorCategoria');
if (ctxCategorias) {
  new Chart(ctxCategorias, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($categorias); ?>,
      datasets: [{
        label: 'Publicaciones',
        data: <?php echo json_encode($totalesCat); ?>,
        backgroundColor: [
          colors.primary,
          colors.info,
          colors.success,
          colors.warning,
          colors.gray
        ],
        borderRadius: 8,
        barThickness: 40
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: '#1A1A1A',
          padding: 12,
          cornerRadius: 8,
          titleFont: {
            size: 13,
            weight: '600'
          },
          bodyFont: {
            size: 14,
            weight: '700'
          },
          callbacks: {
            label: function(context) {
              return context.parsed.x + ' publicaciones';
            }
          }
        }
      },
      scales: {
        x: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
            font: {
              size: 12
            }
          },
          grid: {
            color: '#F3F4F6'
          }
        },
        y: {
          ticks: {
            font: {
              size: 13,
              weight: '600'
            }
          },
          grid: {
            display: false
          }
        }
      }
    }
  });
}
</script>

<?php layout('footer'); ?>
