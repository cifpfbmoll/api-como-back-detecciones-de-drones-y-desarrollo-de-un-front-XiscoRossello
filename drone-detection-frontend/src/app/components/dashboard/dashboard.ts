import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../services/api';
import { Stats, Detection } from '../../models';

@Component({
  selector: 'app-dashboard',
  imports: [CommonModule],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.scss',
})
export class Dashboard implements OnInit {
  stats: Stats | null = null;
  latestDetections: Detection[] = [];
  loading = true;
  error: string | null = null;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadData();
  }

  loadData(): void {
    this.loading = true;
    this.error = null;

    this.apiService.getStats().subscribe({
      next: (response) => {
        this.stats = response.data;
      },
      error: (err) => {
        this.error = 'Error al cargar las estadísticas';
        console.error(err);
      }
    });

    this.apiService.getLatestDetections().subscribe({
      next: (response) => {
        this.latestDetections = response.data;
        this.loading = false;
      },
      error: (err) => {
        this.error = 'Error al cargar las detecciones';
        this.loading = false;
        console.error(err);
      }
    });
  }

  getRssiClass(rssi: number): string {
    if (rssi > -50) return 'signal-strong';
    if (rssi > -70) return 'signal-medium';
    return 'signal-weak';
  }

  getRssiLabel(rssi: number): string {
    if (rssi > -50) return 'Fuerte';
    if (rssi > -70) return 'Media';
    return 'Débil';
  }
}
