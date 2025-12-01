import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { 
  Detection, 
  DetectionResponse, 
  DetectionCreate, 
  DetectionCreateResponse,
  ManufacturerResponse, 
  StatsResponse 
} from '../models';

@Injectable({
  providedIn: 'root',
})
export class ApiService {
  private baseUrl = 'http://localhost:8080/api/v1';

  constructor(private http: HttpClient) {}

  // Detections
  getDetections(page: number = 1, limit: number = 20, manufacturerId?: number, location?: string): Observable<DetectionResponse> {
    let params = new HttpParams()
      .set('page', page.toString())
      .set('limit', limit.toString());
    
    if (manufacturerId) {
      params = params.set('manufacturer_id', manufacturerId.toString());
    }
    if (location) {
      params = params.set('location', location);
    }

    return this.http.get<DetectionResponse>(`${this.baseUrl}/detections`, { params });
  }

  getLatestDetections(): Observable<{ status: number; data: Detection[] }> {
    return this.http.get<{ status: number; data: Detection[] }>(`${this.baseUrl}/detections/latest`);
  }

  createDetection(detection: DetectionCreate): Observable<DetectionCreateResponse> {
    return this.http.post<DetectionCreateResponse>(`${this.baseUrl}/detections`, detection);
  }

  // Manufacturers
  getManufacturers(): Observable<ManufacturerResponse> {
    return this.http.get<ManufacturerResponse>(`${this.baseUrl}/manufacturers`);
  }

  // Stats
  getStats(): Observable<StatsResponse> {
    return this.http.get<StatsResponse>(`${this.baseUrl}/stats`);
  }
}
