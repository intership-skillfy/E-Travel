import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExursionsComponent } from './exursions.component';

describe('ExursionsComponent', () => {
  let component: ExursionsComponent;
  let fixture: ComponentFixture<ExursionsComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ExursionsComponent]
    });
    fixture = TestBed.createComponent(ExursionsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
