import { Routes } from '@angular/router';
import { Dashboard } from './components/dashboard/dashboard';
import { Detections } from './components/detections/detections';
import { Manufacturers } from './components/manufacturers/manufacturers';

export const routes: Routes = [
  { path: '', redirectTo: '/dashboard', pathMatch: 'full' },
  { path: 'dashboard', component: Dashboard },
  { path: 'detections', component: Detections },
  { path: 'manufacturers', component: Manufacturers },
  { path: '**', redirectTo: '/dashboard' }
];
