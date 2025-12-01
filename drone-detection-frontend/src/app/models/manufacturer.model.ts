export interface Manufacturer {
  id: number;
  oui: string;
  name: string;
  created_at: string;
  updated_at: string;
}

export interface ManufacturerResponse {
  status: number;
  data: Manufacturer[];
}
