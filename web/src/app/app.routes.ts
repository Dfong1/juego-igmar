import { Routes } from '@angular/router';
import { authGuard } from './guards/auth.guard';

export const routes: Routes = [
    { path: '', redirectTo: 'login', pathMatch: 'full' },
    { path: 'login', loadComponent: () => import('./components/login/login.component').then(l => l.LoginComponent)  },
    { path: 'verificar-codigo', loadComponent: () => import('./components/codigo/codigo.component').then(c => c.CodigoComponent)},
    { path: 'register', loadComponent: () => import('./components/register/register.component').then(r => r.RegisterComponent) },
    { path: 'dashboard', loadComponent: () =>  import('./components/dashboard/dashboard.component').then(d => d.DashboardComponent), canActivate: [authGuard]},
    { path: 'estadisticas', loadComponent: () => import('./components/estadisticas/estadisticas.component').then(e => e.EstadisticasComponent), canActivate: [authGuard]},
    { path: 'search', loadComponent: () => import('./components/searching/searching.component').then(s => s.SearchingComponent), canActivate: [authGuard] },
    { path: 'juego', loadComponent: () => import('./components/juego/juego.component').then(j => j.JuegoComponent), canActivate: [authGuard] },
    { path: '**', loadComponent: () => import('./components/notfound/notfound.component').then(n => n.NotfoundComponent)}
];
