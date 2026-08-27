@extends('layouts.app')

@section('title', 'Network Topology')

@push('styles')
<style>
    #topologyCanvas {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 12px;
        border: 1px solid #dee2e6;
        cursor: grab;
        width: 100%;
        height: 600px;
    }
    #topologyCanvas:active {
        cursor: grabbing;
    }
    .node-olt { fill: #0d6efd; }
    .node-ont { fill: #6f42c1; }
    .node-splitter { fill: #20c997; }
    .node-switch { fill: #fd7e14; }
    .node-router { fill: #dc3545; }
    .node-online { stroke: #28a745; stroke-width: 3; }
    .node-offline { stroke: #dc3545; stroke-width: 3; }
    .node-maintenance { stroke: #ffc107; stroke-width: 3; }
    
    .legend-item {
        display: inline-flex;
        align-items: center;
        margin-right: 20px;
        font-size: 14px;
    }
    .legend-dot {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        margin-right: 8px;
        border: 2px solid #fff;
        box-shadow: 0 0 4px rgba(0,0,0,0.2);
    }
    .tooltip-info {
        position: absolute;
        background: rgba(0,0,0,0.9);
        color: white;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        pointer-events: none;
        display: none;
        z-index: 1000;
        max-width: 300px;
    }
    .tooltip-info strong {
        color: #ffc107;
    }
    .control-btn {
        margin: 0 5px;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-diagram-3"></i> Network Topology</h2>
        <p class="text-muted">Visual representation of your network</p>
    </div>
    <div>
        <button class="btn btn-primary btn-sm" onclick="autoLayout()">
            <i class="bi bi-arrow-repeat"></i> Auto Layout
        </button>
        <button class="btn btn-success btn-sm" onclick="resetZoom()">
            <i class="bi bi-zoom-in"></i> Reset View
        </button>
        <button class="btn btn-info btn-sm" onclick="exportDiagram()">
            <i class="bi bi-download"></i> Export
        </button>
    </div>
</div>

<!-- Topology Canvas -->
<div class="card">
    <div class="card-body">
        <div class="position-relative">
            <canvas id="topologyCanvas"></canvas>
            <div id="tooltip" class="tooltip-info"></div>
        </div>
    </div>
</div>

<!-- Legend -->
<div class="card mt-4">
    <div class="card-body">
        <h6 class="mb-3">Legend</h6>
        <div>
            <span class="legend-item">
                <span class="legend-dot" style="background: #0d6efd;"></span>
                OLT
            </span>
            <span class="legend-item">
                <span class="legend-dot" style="background: #6f42c1;"></span>
                ONT
            </span>
            <span class="legend-item">
                <span class="legend-dot" style="background: #20c997;"></span>
                Splitter
            </span>
            <span class="legend-item">
                <span class="legend-dot" style="background: #fd7e14;"></span>
                Switch
            </span>
            <span class="legend-item">
                <span class="legend-dot" style="background: #dc3545;"></span>
                Router
            </span>
            <span class="legend-item">
                <span class="legend-dot" style="border-color: #28a745; border-width: 3px;"></span>
                Online
            </span>
            <span class="legend-item">
                <span class="legend-dot" style="border-color: #dc3545; border-width: 3px;"></span>
                Offline
            </span>
            <span class="legend-item">
                <span class="legend-dot" style="border-color: #ffc107; border-width: 3px;"></span>
                Maintenance
            </span>
        </div>
    </div>
</div>

<script>
// Topology data
const nodes = @json($nodes);
const links = @json($links);

const canvas = document.getElementById('topologyCanvas');
const ctx = canvas.getContext('2d');
const tooltip = document.getElementById('tooltip');

let scale = 1;
let offsetX = 0;
let offsetY = 0;
let isDragging = false;
let startX, startY;
let dragNode = null;
let dragOffsetX, dragOffsetY;
let hoveredNode = null;

// Set canvas size
function resizeCanvas() {
    const rect = canvas.parentElement.getBoundingClientRect();
    canvas.width = rect.width - 30;
    canvas.height = 600;
    drawTopology();
}
window.addEventListener('resize', resizeCanvas);

// Get node color based on type
function getNodeColor(type) {
    const colors = {
        'olt': '#0d6efd',
        'ont': '#6f42c1',
        'splitter': '#20c997',
        'switch': '#fd7e14',
        'router': '#dc3545',
        'unknown': '#6c757d'
    };
    return colors[type] || colors['unknown'];
}

// Get node status color
function getStatusColor(status) {
    const colors = {
        'online': '#28a745',
        'offline': '#dc3545',
        'maintenance': '#ffc107'
    };
    return colors[status] || '#6c757d';
}

// Draw the topology
function drawTopology() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    ctx.save();
    ctx.translate(offsetX, offsetY);
    ctx.scale(scale, scale);
    
    // Draw links
    links.forEach(link => {
        const source = nodes.find(n => n.id === link.source);
        const target = nodes.find(n => n.id === link.target);
        
        if (source && target) {
            // Draw line
            ctx.beginPath();
            ctx.moveTo(source.x, source.y);
            ctx.lineTo(target.x, target.y);
            ctx.strokeStyle = '#6c757d';
            ctx.lineWidth = 2 / scale;
            ctx.setLineDash([]);
            ctx.stroke();
            
            // Draw arrow
            const angle = Math.atan2(target.y - source.y, target.x - source.x);
            const arrowSize = 10 / scale;
            const endX = target.x - 20 * Math.cos(angle);
            const endY = target.y - 20 * Math.sin(angle);
            
            ctx.beginPath();
            ctx.moveTo(endX, endY);
            ctx.lineTo(endX - arrowSize * Math.cos(angle - 0.5), endY - arrowSize * Math.sin(angle - 0.5));
            ctx.moveTo(endX, endY);
            ctx.lineTo(endX - arrowSize * Math.cos(angle + 0.5), endY - arrowSize * Math.sin(angle + 0.5));
            ctx.strokeStyle = '#6c757d';
            ctx.lineWidth = 2 / scale;
            ctx.stroke();
            
            // Animated pulse for active links
            const pulse = Math.sin(Date.now() / 1000) * 0.3 + 0.7;
            ctx.beginPath();
            ctx.moveTo(source.x, source.y);
            ctx.lineTo(target.x, target.y);
            ctx.strokeStyle = `rgba(13, 110, 253, ${pulse * 0.2})`;
            ctx.lineWidth = 4 / scale;
            ctx.setLineDash([5 / scale, 5 / scale]);
            ctx.stroke();
            ctx.setLineDash([]);
        }
    });
    
    // Draw nodes
    nodes.forEach(node => {
        const radius = node.type === 'olt' ? 35 / scale : 25 / scale;
        const color = getNodeColor(node.type);
        const statusColor = getStatusColor(node.status);
        
        // Shadow
        ctx.shadowColor = 'rgba(0,0,0,0.2)';
        ctx.shadowBlur = 10 / scale;
        
        // Circle
        const gradient = ctx.createRadialGradient(
            node.x - radius/3, node.y - radius/3, 0,
            node.x, node.y, radius
        );
        gradient.addColorStop(0, '#ffffff');
        gradient.addColorStop(0.3, color);
        gradient.addColorStop(1, color);
        
        ctx.beginPath();
        ctx.arc(node.x, node.y, radius, 0, 2 * Math.PI);
        ctx.fillStyle = gradient;
        ctx.fill();
        ctx.strokeStyle = statusColor;
        ctx.lineWidth = 3 / scale;
        ctx.stroke();
        
        ctx.shadowBlur = 0;
        
        // Label
        ctx.fillStyle = '#ffffff';
        ctx.font = `bold ${12 / scale}px Arial`;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(node.name, node.x, node.y);
        
        // Type label below
        ctx.fillStyle = '#6c757d';
        ctx.font = `${10 / scale}px Arial`;
        ctx.fillText(node.type_name || node.type, node.x, node.y + radius + 16 / scale);
        
        // Subscriber count badge
        if (node.subscribers_count > 0) {
            const badgeRadius = 12 / scale;
            ctx.beginPath();
            ctx.arc(node.x + radius, node.y - radius, badgeRadius, 0, 2 * Math.PI);
            ctx.fillStyle = '#28a745';
            ctx.fill();
            ctx.fillStyle = '#ffffff';
            ctx.font = `bold ${10 / scale}px Arial`;
            ctx.fillText(node.subscribers_count, node.x + radius, node.y - radius);
        }
    });
    
    ctx.restore();
}

// Animation loop
let animationId = null;

function animate() {
    drawTopology();
    animationId = requestAnimationFrame(animate);
}

// Start animation
animate();

// Mouse events for dragging nodes
canvas.addEventListener('mousedown', (e) => {
    const rect = canvas.getBoundingClientRect();
    const mouseX = (e.clientX - rect.left - offsetX) / scale;
    const mouseY = (e.clientY - rect.top - offsetY) / scale;
    
    // Check if clicked on a node
    dragNode = nodes.find(node => {
        const dx = mouseX - node.x;
        const dy = mouseY - node.y;
        const radius = node.type === 'olt' ? 35 : 25;
        return Math.sqrt(dx*dx + dy*dy) < radius;
    });
    
    if (dragNode) {
        dragOffsetX = mouseX - dragNode.x;
        dragOffsetY = mouseY - dragNode.y;
        canvas.style.cursor = 'grabbing';
    } else {
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;
    }
});

canvas.addEventListener('mousemove', (e) => {
    const rect = canvas.getBoundingClientRect();
    const mouseX = (e.clientX - rect.left - offsetX) / scale;
    const mouseY = (e.clientY - rect.top - offsetY) / scale;
    
    if (dragNode) {
        dragNode.x = mouseX - dragOffsetX;
        dragNode.y = mouseY - dragOffsetY;
        drawTopology();
    } else if (isDragging) {
        const dx = e.clientX - startX;
        const dy = e.clientY - startY;
        offsetX += dx;
        offsetY += dy;
        startX = e.clientX;
        startY = e.clientY;
        drawTopology();
    } else {
        // Check hover
        const hovered = nodes.find(node => {
            const dx = mouseX - node.x;
            const dy = mouseY - node.y;
            const radius = node.type === 'olt' ? 35 : 25;
            return Math.sqrt(dx*dx + dy*dy) < radius;
        });
        
        if (hovered) {
            canvas.style.cursor = 'pointer';
            tooltip.style.display = 'block';
            tooltip.innerHTML = `
                <strong>${hovered.name}</strong><br>
                Type: ${hovered.type_name || hovered.type}<br>
                Status: ${hovered.status}<br>
                Subscribers: ${hovered.subscribers_count || 0}
            `;
            tooltip.style.left = (e.clientX + 15) + 'px';
            tooltip.style.top = (e.clientY - 10) + 'px';
            hoveredNode = hovered;
        } else {
            canvas.style.cursor = 'grab';
            tooltip.style.display = 'none';
            hoveredNode = null;
        }
    }
});

canvas.addEventListener('mouseup', () => {
    if (dragNode) {
        dragNode = null;
        canvas.style.cursor = 'grab';
    }
    if (isDragging) {
        isDragging = false;
    }
});

canvas.addEventListener('mouseleave', () => {
    isDragging = false;
    dragNode = null;
    tooltip.style.display = 'none';
});

// Auto layout - force directed simulation
function autoLayout() {
    const iterations = 100;
    const repulsion = 300;
    const attraction = 0.05;
    const damping = 0.9;
    const width = canvas.width;
    const height = canvas.height;
    
    // Initialize velocities
    const velocities = {};
    nodes.forEach(node => {
        velocities[node.id] = { x: 0, y: 0 };
    });
    
    for (let iter = 0; iter < iterations; iter++) {
        // Calculate forces
        nodes.forEach((node, i) => {
            let fx = 0, fy = 0;
            
            // Repulsion from all other nodes
            nodes.forEach((other, j) => {
                if (i === j) return;
                const dx = node.x - other.x;
                const dy = node.y - other.y;
                const dist = Math.sqrt(dx*dx + dy*dy);
                if (dist < 1) return;
                
                const force = repulsion / (dist * dist);
                fx += (dx / dist) * force;
                fy += (dy / dist) * force;
            });
            
            // Attraction from connected nodes
            links.forEach(link => {
                let otherId = null;
                if (link.source === node.id) otherId = link.target;
                if (link.target === node.id) otherId = link.source;
                if (!otherId) return;
                
                const other = nodes.find(n => n.id === otherId);
                if (!other) return;
                
                const dx = other.x - node.x;
                const dy = other.y - node.y;
                const dist = Math.sqrt(dx*dx + dy*dy);
                if (dist < 1) return;
                
                const force = attraction * dist;
                fx += (dx / dist) * force;
                fy += (dy / dist) * force;
            });
            
            // Apply forces with damping
            velocities[node.id].x = (velocities[node.id].x + fx) * damping;
            velocities[node.id].y = (velocities[node.id].y + fy) * damping;
        });
        
        // Update positions
        nodes.forEach(node => {
            node.x += velocities[node.id].x;
            node.y += velocities[node.id].y;
            
            // Keep within bounds
            node.x = Math.max(50, Math.min(width - 50, node.x));
            node.y = Math.max(50, Math.min(height - 50, node.y));
        });
    }
    
    drawTopology();
}

// Reset zoom and position
function resetZoom() {
    scale = 1;
    offsetX = 0;
    offsetY = 0;
    drawTopology();
}

// Export as PNG
function exportDiagram() {
    const link = document.createElement('a');
    link.download = 'network-topology.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
}

// Zoom with mouse wheel
canvas.addEventListener('wheel', (e) => {
    e.preventDefault();
    const rect = canvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;
    
    const delta = e.deltaY > 0 ? 0.9 : 1.1;
    const newScale = Math.max(0.3, Math.min(2, scale * delta));
    
    offsetX = mouseX - (mouseX - offsetX) * (newScale / scale);
    offsetY = mouseY - (mouseY - offsetY) * (newScale / scale);
    scale = newScale;
    
    drawTopology();
}, { passive: false });

// Initialize
setTimeout(() => {
    resizeCanvas();
    autoLayout();
}, 100);

// Redraw on window resize
window.addEventListener('resize', resizeCanvas);
</script>
@endsection