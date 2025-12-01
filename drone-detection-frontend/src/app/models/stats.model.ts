export interface Stats {
  total_detections: number;
  known_drones_count: number;
  unknown_devices_count: number;
  top_manufacturer: string | null;
}

export interface StatsResponse {
  status: number;
  data: Stats;
}
