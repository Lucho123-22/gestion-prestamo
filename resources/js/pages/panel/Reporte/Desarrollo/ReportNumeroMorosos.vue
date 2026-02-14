<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { FilterMatchMode } from '@primevue/core/api';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Message from 'primevue/message';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';

interface Cliente {
    id: number;
    nombre: string;
    telefono: string;
}

interface Moroso {
    prestamo_id: number;
    referencia_prestamo: string;
    cliente: Cliente;
    cuota_id: number;
    numero_cuota: number;
    capital: number;
    saldo_capital: number;
    monto_interes_pagar: number;
    total_deuda: number;
    dias_vencidos: number;
    fecha_inicio: string;
    estado: string;
}

const cuotasMorosas = ref<Moroso[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);
const totalMorosos = ref(0);

// Inicializar filters correctamente
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const loadData = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/morosos');
        cuotasMorosas.value = response.data.data;
        totalMorosos.value = response.data.data.length;
        error.value = null;
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Error al cargar las cuotas morosas';
        cuotasMorosas.value = [];
    } finally {
        loading.value = false;
    }
};

const irAPagar = (prestamoId: number) => {
    router.visit(`/prestamo/${prestamoId}/cliente`);
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: 'PEN'
    }).format(value);
};

const getSeverityDiasVencidos = (dias: number) => {
    if (dias >= 60) return 'danger';
    if (dias >= 45) return 'warn';
    return 'info';
};

onMounted(() => {
    loadData();
});
</script>

<template>
    <Message v-if="error" severity="error" :closable="false">
        {{ error }}
    </Message>

    <div v-else>
        <div class="mb-4 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-semibold mb-2">Cuotas Morosas</h3>
                <p class="text-surface-500">Total: {{ totalMorosos }} cuotas vencidas (más de 30 días)</p>
            </div>
            <IconField>
                <InputIcon>
                    <i class="pi pi-search" />
                </InputIcon>
                <InputText 
                    v-model="filters['global'].value" 
                    placeholder="Buscar cliente..." 
                />
            </IconField>
        </div>

        <DataTable 
            :value="cuotasMorosas" 
            :loading="loading"
            :filters="filters"
            :globalFilterFields="['cliente.nombre', 'referencia_prestamo', 'cliente.telefono']"
            paginator 
            :rows="10"
            :rowsPerPageOptions="[5, 10, 20, 50]"
            stripedRows
            responsiveLayout="scroll"
            dataKey="cuota_id"
        >
            <template #empty>
                <div class="text-center p-4">
                    <i class="pi pi-check-circle text-4xl text-green-500 mb-3"></i>
                    <p class="text-surface-500">¡No hay cuotas morosas!</p>
                </div>
            </template>

            <Column field="referencia_prestamo" header="Referencia" sortable style="min-width: 150px">
                <template #body="{ data }">
                    <span class="font-semibold">{{ data.referencia_prestamo }}</span>
                </template>
            </Column>

            <Column field="cliente.nombre" header="Cliente" sortable style="min-width: 200px">
                <template #body="{ data }">
                    <div>
                        <div class="font-semibold">{{ data.cliente.nombre }}</div>
                        <div class="text-sm text-surface-500">{{ data.cliente.telefono }}</div>
                    </div>
                </template>
            </Column>

            <Column field="numero_cuota" header="Cuota" sortable style="min-width: 80px">
                <template #body="{ data }">
                    <Tag severity="danger">#{{ data.numero_cuota }}</Tag>
                </template>
            </Column>

            <Column field="saldo_capital" header="Capital" sortable style="min-width: 120px">
                <template #body="{ data }">
                    <span class="font-semibold">{{ formatCurrency(data.saldo_capital) }}</span>
                </template>
            </Column>

            <Column field="monto_interes_pagar" header="Interés" sortable style="min-width: 120px">
                <template #body="{ data }">
                    {{ formatCurrency(data.monto_interes_pagar) }}
                </template>
            </Column>

            <Column field="total_deuda" header="Total Deuda" sortable style="min-width: 130px">
                <template #body="{ data }">
                    <span class="font-bold text-red-600">{{ formatCurrency(data.total_deuda) }}</span>
                </template>
            </Column>

            <Column field="dias_vencidos" header="Días Vencidos" sortable style="min-width: 130px" alignHeader="center">
                <template #body="{ data }">
                    <div class="text-center">
                        <Tag :severity="getSeverityDiasVencidos(data.dias_vencidos)">
                            {{ data.dias_vencidos }} días
                        </Tag>
                    </div>
                </template>
            </Column>

            <Column field="fecha_inicio" header="Fecha Inicio" sortable style="min-width: 120px">
                <template #body="{ data }">
                    <span class="text-sm">{{ new Date(data.fecha_inicio).toLocaleDateString('es-PE') }}</span>
                </template>
            </Column>

            <Column header="Acciones" style="min-width: 120px" alignHeader="center">
                <template #body="{ data }">
                    <div class="flex justify-center">
                        <Button 
                            label="Ir a Pagar" 
                            icon="pi pi-money-bill" 
                            size="small"
                            severity="danger"
                            @click="irAPagar(data.prestamo_id)"
                        />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>