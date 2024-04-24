import { Routes } from '@angular/router';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { NotfoundComponent } from './components/notfound/notfound.component';
import { DashboardComponent } from './components/dashboard/dashboard.component';
import { EstadisticasComponent } from './components/estadisticas/estadisticas.component';
import { SearchingComponent } from './components/searching/searching.component';
import { CodigoComponent } from './components/codigo/codigo.component';
import { loginGuard } from './guards/login.guard';
import { authGuard } from './guards/auth.guard';
import { JuegoComponent } from './components/juego/juego.component';

export const routes: Routes = [
    { path: '', redirectTo: 'login', pathMatch: 'full' },
    { path: 'login', component: LoginComponent },
    { path: 'verificar-codigo', component: CodigoComponent},
    { path: 'register', component: RegisterComponent },
    { path: 'dashboard', component: DashboardComponent, canActivate: [authGuard]},
    { path: 'estadisticas', component: EstadisticasComponent, canActivate: [authGuard]},
    { path: 'search', component: SearchingComponent },
    { path: 'juego', component: JuegoComponent },
    { path: '**', component: NotfoundComponent}
];
