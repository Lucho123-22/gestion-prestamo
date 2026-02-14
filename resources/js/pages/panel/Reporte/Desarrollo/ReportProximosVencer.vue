<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
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

interface ProximaVencer {
    prestamo_id: number;
    prestamo_referencia: string;
    cliente_id: number;
    cliente_nombre: string;
    cliente_telefono: string;
    cuota_id: number;
    numero_cuota: number;
    saldo_capital: number;
    interes_pendiente: number;
    total_deuda: number;
    dias_transcurridos: number;
    dias_para_vencer: number;
    fecha_inicio: string;
    estado: string;
}

const cuotasProximasVencer = ref<ProximaVencer[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);
const globalFilter = ref('');
const totalCuotas = ref(0);

const loadData = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/proximos-vencer');
        cuotasProximasVencer.value = response.data.data;
        totalCuotas.value = response.data.data.length;
        error.value = null;
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Error al cargar las cuotas próximas a vencer';
        cuotasProximasVencer.value = [];
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

const getSeverityDias = (dias: number) => {
    if (dias <= 5) return 'danger';
    if (dias <= 10) return 'warn';
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
                    <h3 class="text-xl font-semibold mb-2">Cuotas Próximas a Vencer</h3>
                    <p class="text-surface-500">Total: {{ totalCuotas }} cuotas entre 16-29 días</p>
                </div>
                <IconField>
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText 
                        v-model="globalFilter" 
                        placeholder="Buscar cliente..." 
                    />
                </IconField>
            </div>

            <DataTable 
                :value="cuotasProximasVencer" 
                :loading="loading"
                :globalFilterFields="['cliente_nombre', 'prestamo_referencia', 'cliente_telefono']"
                :globalFilter="globalFilter"
                paginator 
                :rows="10"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                stripedRows
                responsiveLayout="scroll"
                dataKey="cuota_id"
            >
                <template #empty>
                    <div class="text-center p-4">
                        <i class="pi pi-inbox text-4xl text-surface-300 mb-3"></i>
                        <p class="text-surface-500">No hay cuotas próximas a vencer</p>
                    </div>
                </template>

                <Column field="prestamo_referencia" header="Referencia" sortable style="min-width: 150px">
                    <template #body="{ data }">
                        <span class="font-semibold">{{ data.prestamo_referencia }}</span>
                    </template>
                </Column>

                <Column field="cliente_nombre" header="Cliente" sortable style="min-width: 200px">
                    <template #body="{ data }">
                        <div>
                            <div class="font-semibold">{{ data.cliente_nombre }}</div>
                            <div class="text-sm text-surface-500">{{ data.cliente_telefono }}</div>
                        </div>
                    </template>
                </Column>

                <Column field="numero_cuota" header="Cuota" sortable style="min-width: 80px">
                    <template #body="{ data }">
                        <Tag severity="secondary">#{{ data.numero_cuota }}</Tag>
                    </template>
                </Column>

                <Column field="saldo_capital" header="Capital" sortable style="min-width: 120px">
                    <template #body="{ data }">
                        <span class="font-semibold">{{ formatCurrency(data.saldo_capital) }}</span>
                    </template>
                </Column>

                <Column field="interes_pendiente" header="Interés" sortable style="min-width: 120px">
                    <template #body="{ data }">
                        {{ formatCurrency(data.interes_pendiente) }}
                    </template>
                </Column>

                <Column field="total_deuda" header="Total Deuda" sortable style="min-width: 130px">
                    <template #body="{ data }">
                        <span class="font-bold text-primary">{{ formatCurrency(data.total_deuda) }}</span>
                    </template>
                </Column>

                <Column field="dias_transcurridos" header="Días Transcurridos" sortable style="min-width: 130px" alignHeader="center">
                    <template #body="{ data }">
                        <div class="text-center">
                            <Tag :severity="getSeverityDias(data.dias_para_vencer)">
                                {{ data.dias_transcurridos }} días
                            </Tag>
                        </div>
                    </template>
                </Column>

                <Column field="dias_para_vencer" header="Días para Vencer" sortable style="min-width: 130px" alignHeader="center">
                    <template #body="{ data }">
                        <div class="text-center font-semibold" :class="{
                            'text-red-600': data.dias_para_vencer <= 5,
                            'text-orange-600': data.dias_para_vencer > 5 && data.dias_para_vencer <= 10,
                            'text-blue-600': data.dias_para_vencer > 10
                        }">
                            {{ data.dias_para_vencer }} días
                        </div>
                    </template>
                </Column>

                <Column header="Acciones" style="min-width: 120px" alignHeader="center">
                    <template #body="{ data }">
                        <div class="flex justify-center">
                            <Button 
                                label="Ir a Pagar" 
                                icon="pi pi-money-bill" 
                                size="small"
                                severity="success"
                                @click="irAPagar(data.prestamo_id)"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
</template>