import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ExcursionsComponent } from './excursions.component';
import { ExcursionsListComponent } from './excursions-list/excursions-list.component';
import { NgApexchartsModule } from 'ng-apexcharts';
import { InlineSVGModule } from 'ng-inline-svg-2';
import { RouterModule, Routes } from '@angular/router';
import { OffersRoutingModule } from 'src/app/pages/offers/offers-routing.module';
import { WidgetsModule } from "../../_metronic/partials/content/widgets/widgets.module";
import { ModalFormComponent } from './modal-form/modal-form.component';
import { ModalsModule } from "../../_metronic/partials/layout/modals/modals.module";
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { FormsModule, ReactiveFormsModule } from '@angular/forms'; // Import FormsModule and ReactiveFormsModule
import { NgbModalModule } from '@ng-bootstrap/ng-bootstrap';
import { MatIconModule } from '@angular/material/icon';
import { NgMultiSelectDropDownModule } from 'ng-multiselect-dropdown';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatSelectModule } from '@angular/material/select';
import { ExcursionDetailsComponent } from './excursion-details/excursion-details.component';
import { PriceModalComponent } from './price-modal/price-modal.component';
import { ConfirmationDialogComponent } from './confirmation-dialog/confirmation-dialog.component';
import { MatDialogModule } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
@NgModule({
  declarations: [
    ExcursionsListComponent,
    ExcursionsComponent,
    ModalFormComponent,
    ExcursionDetailsComponent,
    PriceModalComponent,
    ConfirmationDialogComponent,
  ],
  imports: [
    CommonModule,
    NgApexchartsModule,
    InlineSVGModule,
    RouterModule,
    OffersRoutingModule,
    WidgetsModule,
    ModalsModule,
    NgbModule,
    FormsModule,
    NgbModalModule,
    MatIconModule,
    NgMultiSelectDropDownModule.forRoot(),
    MatFormFieldModule,
    MatSelectModule,
    ReactiveFormsModule,
    MatDialogModule,
    MatButtonModule
  ],
  exports: [ExcursionsComponent,ExcursionDetailsComponent],
})
export class ExcursionsModule {}
