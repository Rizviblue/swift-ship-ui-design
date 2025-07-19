import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Index from "./pages/Index";
import NotFound from "./pages/NotFound";
import Login from "./pages/Login";
import AddCourier from "./pages/admin/AddCourier";
import CourierList from "./pages/admin/CourierList";
import AgentManagement from "./pages/admin/AgentManagement";
import CustomerManagement from "./pages/admin/CustomerManagement";
import Reports from "./pages/admin/Reports";
import Settings from "./pages/admin/Settings";
import TrackCourier from "./pages/customer/TrackCourier";

const queryClient = new QueryClient();

const App = () => (
  <QueryClientProvider client={queryClient}>
    <TooltipProvider>
      <Toaster />
      <Sonner />
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<Index />} />
          <Route path="/login" element={<Login />} />
          
          {/* Admin Routes */}
          <Route path="/admin" element={<Index />} />
          <Route path="/admin/add-courier" element={<AddCourier />} />
          <Route path="/admin/couriers" element={<CourierList />} />
          <Route path="/admin/agents" element={<AgentManagement />} />
          <Route path="/admin/customers" element={<CustomerManagement />} />
          <Route path="/admin/reports" element={<Reports />} />
          <Route path="/admin/settings" element={<Settings />} />
          
          {/* Customer Routes */}
          <Route path="/customer" element={<TrackCourier />} />
          <Route path="/customer/track" element={<TrackCourier />} />
          
          {/* ADD ALL CUSTOM ROUTES ABOVE THE CATCH-ALL "*" ROUTE */}
          <Route path="*" element={<NotFound />} />
        </Routes>
      </BrowserRouter>
    </TooltipProvider>
  </QueryClientProvider>
);

export default App;
