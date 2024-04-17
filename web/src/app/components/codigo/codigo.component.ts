import { HttpClient } from '@angular/common/http';
import { Component } from '@angular/core';
import { LoginService } from '../../services/login.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-codigo',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './codigo.component.html',
  styleUrl: './codigo.component.css'
})
export class CodigoComponent {

  constructor(private http: HttpClient, private ls: LoginService, private router: Router) { }

  public codigo: string = ""
  public message: string|null = ""

  verificar() {
    this.ls.verificarCodigo(this.codigo).subscribe(
      (response) => {
        console.log(response)
        this.message = `Codigo correcto
        Redireccionando a pantalla principal`
        
        setTimeout(() => {
          this.router.navigate(['/dashboard']);
        }, 1500)
      }, (error) => {
        console.log(error)
      }
    )
  }

}
