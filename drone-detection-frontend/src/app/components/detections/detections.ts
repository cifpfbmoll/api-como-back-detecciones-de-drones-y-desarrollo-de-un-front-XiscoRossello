import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api';
import { Detection, Manufacturer, DetectionCreate } from '../../models';

@Component({
  selector: 'app-detections',
  imports: [CommonModule, FormsModule],
  templateUrl: './detections.html',
  styleUrl: './detections.scss',
})
export class Detections implements OnInit {
  detections: Detection[] = [];
  manufacturers: Manufacturer[] = [];
  loading = true;
  error: string | null = null;
  
  // Pagination
  currentPage = 1;
  totalPages = 1;
  perPage = 10;
  total = 0;

  // Filters
  selectedManufacturer: number | null = null;
  locationFilter = '';

  // New Detection Form
  showForm = false;
  newDetection: DetectionCreate = {
    mac: '',
    rssi: -50,
    sensor_location: ''
  };
  submitting = false;
  formError: string | null = null;
  formSuccess: string | null = null;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadManufacturers();
    this.loadDetections();
  }

  loadManufacturers(): void {
    this.apiService.getManufacturers().subscribe({
      next: (response) => {
        this.manufacturers = response.data;
      },
      error: (err) => console.error('Error loading manufacturers:', err)
    });
  }

  loadDetections(): void {
    this.loading = true;
    this.error = null;

    this.apiService.getDetections(
      this.currentPage, 
      this.perPage, 
      this.selectedManufacturer ?? undefined,
      this.locationFilter || undefined
    ).subscribe({
      next: (response) => {
        this.detections = response.data;
        if (response.pagination) {
          this.currentPage = response.pagination.current_page;
          this.totalPages = response.pagination.total_pages;
          this.total = response.pagination.total;
        }
        this.loading = false;
      },
      error: (err) => {
        this.error = 'Error al cargar las detecciones';
        this.loading = false;
        console.error(err);
      }
    });
  }

  applyFilters(): void {
    this.currentPage = 1;
    this.loadDetections();
  }

  clearFilters(): void {
    this.selectedManufacturer = null;
    this.locationFilter = '';
    this.currentPage = 1;
    this.loadDetections();
  }

  goToPage(page: number): void {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.loadDetections();
    }
  }

  toggleForm(): void {
    this.showForm = !this.showForm;
    this.formError = null;
    this.formSuccess = null;
    if (this.showForm) {
      this.newDetection = { mac: '', rssi: -50, sensor_location: '' };
    }
  }

  submitDetection(): void {
    if (!this.newDetection.mac || !this.newDetection.sensor_location) {
      this.formError = 'Por favor, complete todos los campos requeridos';
      return;
    }

    this.submitting = true;
    this.formError = null;
    this.formSuccess = null;

    this.apiService.createDetection(this.newDetection).subscribe({
      next: (response) => {
        this.formSuccess = `Detección registrada correctamente (ID: ${response.data.id})`;
        this.submitting = false;
        this.newDetection = { mac: '', rssi: -50, sensor_location: '' };
        this.loadDetections();
        setTimeout(() => {
          this.showForm = false;
          this.formSuccess = null;
        }, 2000);
      },
      error: (err) => {
        this.formError = 'Error al registrar la detección';
        this.submitting = false;
        console.error(err);
      }
    });
  }

  getRssiClass(rssi: number): string {
    if (rssi > -50) return 'signal-strong';
    if (rssi > -70) return 'signal-medium';
    return 'signal-weak';
  }
}
