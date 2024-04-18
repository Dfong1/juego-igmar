import { Component, OnInit } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { UserService } from '../../services/user.service';
import { User } from '../../Interfaces/user-interface';

@Component({
  selector: 'app-estadisticas',
  standalone: true,
  imports: [NavbarComponent],
  templateUrl: './estadisticas.component.html',
  styleUrl: './estadisticas.component.css'
})
export class EstadisticasComponent implements OnInit {

  constructor( private us: UserService ) {}

  public user: User = {
    id: 0,
    email: "",
    name: "",
    codigoVerificadO: false,
    created_at: "",
    is_active: false,
    updated_at: ""
  }

  ngOnInit(): void {
    this.us.getData().subscribe(
      (response) => {
        this.user = response
      }
    )
  }

}
