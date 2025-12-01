export interface Detection {
  id: number;
  mac_address: string;
  manufacturer_id: number | null;
  rssi: number;
  sensor_location: string;
  detected_at: string;
  created_at: string;
  manufacturer_name: string | null;
  oui: string | null;
}

export interface DetectionCreate {
  mac: string;
  rssi: number;
  sensor_location: string;
  timestamp?: string;
}

export interface DetectionResponse {
  status: number;
  data: Detection[];
  pagination?: {
    current_page: number;
    per_page: number;
    total: number;
    total_pages: number;
  };
}

export interface DetectionCreateResponse {
  status: number;
  message: string;
  data: Detection;
}
