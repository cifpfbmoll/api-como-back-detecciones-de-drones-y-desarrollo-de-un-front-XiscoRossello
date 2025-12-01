import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api';
import { Manufacturer } from '../../models';

@Component({
  selector: 'app-manufacturers',
  imports: [CommonModule, FormsModule],
  templateUrl: './manufacturers.html',
  styleUrl: './manufacturers.scss',
})
export class Manufacturers implements OnInit {
  manufacturers: Manufacturer[] = [];
  filteredManufacturers: Manufacturer[] = [];
  loading = true;
  error: string | null = null;
  searchTerm = '';

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadManufacturers();
  }

  loadManufacturers(): void {
    this.loading = true;
    this.error = null;

    this.apiService.getManufacturers().subscribe({
      next: (response) => {
        this.manufacturers = response.data;
        this.filteredManufacturers = [...this.manufacturers];
        this.loading = false;
      },
      error: (err) => {
        this.error = 'Error al cargar los fabricantes';
        this.loading = false;
        console.error(err);
      }
    });
  }

  filterManufacturers(): void {
    const term = this.searchTerm.toLowerCase();
    this.filteredManufacturers = this.manufacturers.filter(m => 
      m.name.toLowerCase().includes(term) || 
      m.oui.toLowerCase().includes(term)
    );
  }

  clearSearch(): void {
    this.searchTerm = '';
    this.filteredManufacturers = [...this.manufacturers];
  }

  getManufacturerIcon(name: string): string {
    const lowerName = name.toLowerCase();
    if (lowerName.includes('dji')) return '🚁';
    if (lowerName.includes('parrot')) return '🦜';
    if (lowerName.includes('yuneec')) return '✈️';
    if (lowerName.includes('raspberry')) return '🍓';
    if (lowerName.includes('espressif')) return '📡';
    return '🛸';
  }
}
